from flask import Flask, request, Response
from flask_restful import Resource, Api
from models import db, init_db, Capital, Order, OrderItem, Payment, User
from flask_cors import CORS
from sqlalchemy import func
from decimal import Decimal
from datetime import datetime
import json

app = Flask(__name__)
api = Api(app)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:@localhost:3306/laravel'
CORS(app)

init_db(app)

class CustomJSONEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, datetime):
            return obj.isoformat()
        elif isinstance(obj, Decimal):
            return str(obj)  # Convert Decimal to string
        return super().default(obj)

app.json_encoder = CustomJSONEncoder()

class RekapResource(Resource):
    def get(self, req_date):
        capital = (
            Capital.query
            .filter(func.DATE(Capital.created_at) == req_date)
            .first()
        )

        cash_transaction = (
            db.session.query(func.sum(OrderItem.amount))
            .join(Order, Order.id == OrderItem.order_id)
            .join(Payment, Order.id == Payment.order_id)
            .filter(OrderItem.payment_method == 'Cash')
            .filter(func.DATE(Order.created_at) == req_date)
            .scalar() or 0
        )
        cashless_transaction = (
            db.session.query(func.sum(OrderItem.amount))
            .join(Order, Order.id == OrderItem.order_id)
            .join(Payment, Order.id == Payment.order_id)
            .filter(OrderItem.payment_method == 'Cashless')
            .filter(func.DATE(Order.created_at) == req_date)
            .scalar() or 0
        )
        total_payment = cash_transaction + cashless_transaction

        rekap = {
            'date': req_date,
            'capital': str(capital.capital) if capital else '0',
            'cash_transaction': str(cash_transaction),
            'cashless_transaction': str(cashless_transaction),
            'total': str(total_payment),
            'total_cash': str(cash_transaction + capital.capital)
        }

        detail = {}

        orders = (
            Order.query
            .filter(func.DATE(Order.created_at) == req_date)
            .all()
        )

        for order in orders:
            order_items = (
                db.session.query(OrderItem)
                .filter(OrderItem.order_id == order.id)
                .all()
            )
            operator = User.query.get(order.user_id).name
            total_price = sum(item.amount for item in order_items)
            payment = Payment.query.filter_by(order_id=order.id).first()

            if payment:
                received_amount = Decimal(payment.amount)
                change = str(received_amount - total_price)
            else:
                received_amount = Decimal(0)
                change = str(0)

            method = order_items[0].payment_method if order_items else ''
            proof = bool(order.proof_image)

            detail[order.id] = {
                'id': order.id,
                'operator': operator,
                'total': str(total_price),
                'received_amount': str(received_amount),
                'change': change,
                'method': method,
                'proof': proof
            }

        response = {
            'rekap': rekap,
            'detail': detail
        }

        return Response(json.dumps(response, cls=CustomJSONEncoder), mimetype='application/json')

api.add_resource(RekapResource, '/rekap/<string:req_date>')

if __name__ == '__main__':
    app.run()