from flask import Flask, request, Response
from flask_restful import Resource, Api
from models import db, init_db, Capital, Order, OrderItem, Payment, User, Product
from flask_cors import CORS
from sqlalchemy import func
from decimal import Decimal
from datetime import datetime, timedelta, date
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

class BoxValueResource(Resource):
    def get(self):
        start_date = request.args.get('start_date')
        end_date = request.args.get('end_date')

        #Get total orders
        if start_date and end_date:
            start_datetime = datetime.strptime(start_date, '%Y-%m-%d')
            end_datetime = datetime.strptime(end_date, '%Y-%m-%d')
            countOrder = Order.query.filter(
                func.date(Order.created_at).between(start_datetime, end_datetime)
            ).count()
        else:
            countOrder = Order.query.count()

        #Get Total Pendapatan
        if start_date and end_date:
            start_datetime = datetime.strptime(start_date, '%Y-%m-%d')
            end_datetime = datetime.strptime(end_date, '%Y-%m-%d')
            countIncome = OrderItem.query.with_entities(func.sum(OrderItem.amount)).filter(
            func.date(OrderItem.created_at).between(start_datetime, end_datetime)
            ).scalar()
        else :
            countIncome = OrderItem.query.with_entities(func.sum(OrderItem.amount)).scalar()

        #Get Total Barang Terjual
        if start_date and end_date:
            start_datetime = datetime.strptime(start_date, '%Y-%m-%d')
            end_datetime = datetime.strptime(end_date, '%Y-%m-%d')
            countQty = OrderItem.query.with_entities(func.sum(OrderItem.quantity)).filter(
            func.date(OrderItem.created_at).between(start_datetime, end_datetime)
            ).scalar()
        else :
            countQty = OrderItem.query.with_entities(func.sum(OrderItem.quantity)).scalar()

        # Get countStockPrice
        countStockPrice = OrderItem.query.with_entities(func.sum(OrderItem.quantity * Product.stock_price)).join(
            Product, OrderItem.product_id == Product.id
        ).scalar()

        profit = countIncome - countStockPrice

        data = {'Total Transaksi': countOrder,
                'Total Barang Terjual': str(countQty),
                'Total Pendapatan' : str(countIncome),
                'Profit': str(profit)
                }
        return data

# Chart 1: Pendapatan Harian kotor-bersih
class IncomeProfitByDay(Resource):
    def get(self):
        start_date = request.args.get('start_date')
        end_date = request.args.get('end_date')

        if not start_date and not end_date:
            # Calculate the current month's start and end dates
            today = date.today()
            start_date = date(today.year, today.month, 1).strftime('%Y-%m-%d')
            next_month = (date(today.year, today.month, 1) + timedelta(days=32)).replace(day=1) - timedelta(days=1)
            end_date = next_month.strftime('%Y-%m-%d')

        start_datetime = datetime.strptime(start_date, '%Y-%m-%d')
        end_datetime = datetime.strptime(end_date, '%Y-%m-%d')

        data = {}
        current_date = start_datetime
        while current_date <= end_datetime:
            next_date = current_date + timedelta(days=1)

            income = OrderItem.query.with_entities(func.sum(OrderItem.amount)).filter(
                func.date(OrderItem.created_at).between(current_date, next_date - timedelta(seconds=1))
            ).scalar()

            stock_price = OrderItem.query.with_entities(func.sum(OrderItem.quantity * Product.stock_price)).join(
                Product, OrderItem.product_id == Product.id
            ).filter(
                func.date(OrderItem.created_at).between(current_date, next_date - timedelta(seconds=1))
            ).scalar()

            profit = income - stock_price if income is not None and stock_price is not None else 0
            if income == None:
                income = 0
            
            data[current_date.strftime('%Y-%m-%d')] = {
                'income': str(income),
                'profit': str(profit)
            }

            current_date = next_date

        return data

# Chart 2 : Perbandingan pengunjung dengan barang terjual
class OrderQuantityByDay(Resource):
    def get(self):
        start_date = request.args.get('start_date')
        end_date = request.args.get('end_date')

        if not start_date and not end_date:
            # Calculate the current month's start and end dates
            today = date.today()
            start_date = date(today.year, today.month, 1).strftime('%Y-%m-%d')
            next_month = (date(today.year, today.month, 1) + timedelta(days=32)).replace(day=1) - timedelta(days=1)
            end_date = next_month.strftime('%Y-%m-%d')

        start_datetime = datetime.strptime(start_date, '%Y-%m-%d')
        end_datetime = datetime.strptime(end_date, '%Y-%m-%d')

        data = {}
        current_date = start_datetime
        while current_date <= end_datetime:
            next_date = current_date + timedelta(days=1)

            order_count = Order.query.filter(
                func.date(Order.created_at).between(current_date, next_date - timedelta(seconds=1))
            ).count()

            qty_count = db.session.query(func.sum(OrderItem.quantity)).join(
                Order, Order.id == OrderItem.order_id
            ).filter(
                func.date(Order.created_at).between(current_date, next_date - timedelta(seconds=1))
            ).scalar()
            if qty_count == None:
                qty_count = 0

            data[current_date.strftime('%Y-%m-%d')] = {
                'order_count': str(order_count),
                'qty_count': str(qty_count)
            }

            current_date = next_date

        return data

# Chart 3 : List  Produk terlaku
class ProductQuantity(Resource):
    def get(self):
        start_date = request.args.get('start_date')
        end_date = request.args.get('end_date')
        limit = int(request.args.get('limit', 5))  # Default limit is set to 5 if not provided

        query = db.session.query(Product.name, func.sum(OrderItem.quantity)).join(
            OrderItem, OrderItem.product_id == Product.id
        ).group_by(Product.name).order_by(func.sum(OrderItem.quantity).desc())

        if start_date and end_date:
            start_datetime = datetime.strptime(start_date, '%Y-%m-%d')
            end_datetime = datetime.strptime(end_date, '%Y-%m-%d')
            query = query.filter(func.date(OrderItem.created_at).between(start_datetime, end_datetime))

        query = query.limit(limit)  # Apply the limit here

        product_counts = query.all()

        data = {}
        for product_name, qty_count in product_counts:
            data[product_name] = {
                'qty_count': str(qty_count)
            }

        return data

# Chart 4 : Pie payment stats
class PaymentStats(Resource):
    def get(self):
        start_date = request.args.get('start_date')
        end_date = request.args.get('end_date')

        query = db.session.query(OrderItem.payment_method, func.sum(OrderItem.amount))

        if start_date and end_date:
            start_datetime = datetime.strptime(start_date, '%Y-%m-%d')
            end_datetime = datetime.strptime(end_date, '%Y-%m-%d')
            query = query.filter(func.date(OrderItem.created_at).between(start_datetime, end_datetime))

        payment_stats = query.group_by(OrderItem.payment_method).all()

        total_amount = sum(amount for _, amount in payment_stats)

        data = {}
        for payment_method, amount in payment_stats:
            percent = (amount / total_amount) * 100 if total_amount != 0 else 0
            data[payment_method] = {
                'total': str(amount),
                'percent': str(percent)
            }

        return data

api.add_resource(PaymentStats, '/payment-stats')
api.add_resource(ProductQuantity, '/product-quantity')
api.add_resource(OrderQuantityByDay, '/order-quantity')
api.add_resource(IncomeProfitByDay, '/income-profit')
api.add_resource(BoxValueResource, '/dashboardbox')

if __name__ == '__main__':
    app.run()