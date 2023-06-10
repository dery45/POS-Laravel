from flask import Flask, request, jsonify
from json import JSONEncoder
from flask_restful import Resource, Api
from flask_cors import CORS
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import text
import json
from datetime import datetime
from sqlalchemy import func

app = Flask(__name__)
api = Api(app)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:@localhost:3306/laravel'
db = SQLAlchemy(app)
CORS(app)

def check_database_connection():
    with app.app_context():
        try:
            db.session.query(text('1')).from_statement(text('SELECT 1')).all()
            print('Database connection successful.')
        except Exception as e:
            print('Error connecting to the database:', str(e))
            # Handle the exception, such as exiting the application or showing an error message to the user

# Call the function to check the database connection
check_database_connection()

class Capital(db.Model):
    __tablename__ = 'daily_capitals'

    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    capital = db.Column(db.DECIMAL(10, 2))
    user_id = db.Column(db.BigInteger)
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    db.Column('id', db.BigInteger, primary_key=True, autoincrement=True, info={'unsigned': True})

    def __init__(self, capital, user_id):
        self.capital = capital
        self.user_id = user_id

class Order(db.Model):
    __tablename__ = 'orders'

    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    customer_id = db.Column(db.BigInteger)
    user_id = db.Column(db.BigInteger)
    proof_image = db.Column(db.String(191))
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    def __init__(self, customer_id, user_id, proof_image):
        self.customer_id = customer_id
        self.user_id = user_id
        self.proof_image = proof_image

class OrderItem(db.Model):
    __tablename__ = 'order_items'

    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    amount = db.Column(db.DECIMAL(14, 2))
    quantity = db.Column(db.Integer)
    payment_method = db.Column(db.Enum('Cash', 'Cashless'))
    order_id = db.Column(db.BigInteger)
    product_id = db.Column(db.BigInteger)
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    def __init__(self, amount, quantity, payment_method, order_id, product_id):
        self.amount = amount
        self.quantity = quantity
        self.payment_method = payment_method
        self.order_id = order_id
        self.product_id = product_id

class Payment(db.Model):
    __tablename__ = 'payments'

    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    amount = db.Column(db.DECIMAL(14, 2))
    order_id = db.Column(db.BigInteger)
    user_id = db.Column(db.BigInteger)
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    def __init__(self, amount, order_id, user_id):
        self.amount = amount
        self.order_id = order_id
        self.user_id = user_id

class User(db.Model):
    __tablename__ = 'users'

    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(255), unique=True, nullable=False)
    name = db.Column(db.String(255), nullable=False)
    address = db.Column(db.String(255), nullable=False)
    phone_number = db.Column(db.String(20), nullable=False)
    fk_role_id = db.Column(db.Integer, db.ForeignKey('roles.role_id'), nullable=False)
    password = db.Column(db.String(255), nullable=False)
    email = db.Column(db.String(255), unique=True, nullable=False)
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    edited_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    def __init__(self, username, name, address, phone_number, fk_role_id, password, email, token):
        self.username = username
        self.name = name
        self.address = address
        self.phone_number = phone_number
        self.fk_role_id = fk_role_id
        self.password = password
        self.email = email

class CustomJSONEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, datetime):
            return obj.isoformat()
        return super().default(obj)

app.json_encoder = CustomJSONEncoder

class GetAllCapitalResource(Resource):
    def get(self):
        capitals = db.session.query(Capital).all()

        data = []
        for cap in capitals:
            cap = {
                'id': cap.id,
                'capital': cap.capital,
                'user_id' : cap.user_id,
                'created_at': cap.created_at,
                'updated_at': cap.updated_at
            }
            data.append(cap)
        return jsonify(data)

class RekapResource(Resource):
    def get(self, req_date):
        capital = Capital.query.filter(Capital.created_at == req_date).first()

        cash_transaction = db.session.query(func.sum(Payment.amount)).join(OrderItem).filter(OrderItem.payment_method == 'Cash').scalar() or 0
        cashless_transaction = db.session.query(func.sum(Payment.amount)).join(OrderItem).filter(OrderItem.payment_method == 'Cashless').scalar() or 0
        total_payment = cash_transaction + cashless_transaction

        rekap = {
            'date': capital.created_at,
            'capital': capital.capital,
            'cash_transaction': cash_transaction,
            'cashless_transaction': cashless_transaction,
            'total': total_payment
        }

        detail = {}

        orders = Order.query.all()

        for order in orders:
            order_items = OrderItem.query.filter_by(order_id=order.id).all()
            operator = User.query.get(order.user_id).name
            total_price = sum(item.amount * item.quantity for item in order_items)
            payment = Payment.query.filter_by(order_id=order.id).first()

            if payment:
                received_amount = payment.amount
                change = received_amount - total_price
            else:
                received_amount = 0
                change = 0

            method = order_items[0].payment_method if order_items else ''
            proof = bool(order.proof)

            detail[order.id] = {
                'id': order.id,
                'operator': operator,
                'total': total_price,
                'received_amount': received_amount,
                'change': change,
                'method': method,
                'proof': proof
            }

        response = {
            'rekap': rekap,
            'detail': detail
        }

        return response

api.add_resource(RekapResource, '/rekap/<string:req_date>')


if __name__ == '__main__':
    app.run()
