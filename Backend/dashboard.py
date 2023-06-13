from flask import Flask, request, Response
from flask_restful import Resource, Api
from models import db, init_db, Capital, Order, OrderItem, Payment, User, Product
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

        #Get Total Laba
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



api.add_resource(BoxValueResource, '/dashboardbox')

if __name__ == '__main__':
    app.run()