from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import text
from datetime import datetime
from decimal import Decimal

db = SQLAlchemy()

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

    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    name = db.Column(db.String(191), nullable=False)
    email = db.Column(db.String(191), unique=True, nullable=False)
    username = db.Column(db.String(191), unique=True, nullable=False)
    password = db.Column(db.String(191), nullable=False)
    address = db.Column(db.String(191), nullable=False)
    phone_number = db.Column(db.String(191), nullable=False)
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    def __init__(self, name, email, username, password, address, phone_number):
        self.name = name
        self.email = email
        self.username = username
        self.password = password
        self.address = address
        self.phone_number = phone_number

class Product(db.Model):
    __tablename__ = 'products'

    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    name = db.Column(db.String(191))
    description = db.Column(db.Text)
    category_product = db.Column(db.BigInteger)
    image = db.Column(db.String(191))
    barcode = db.Column(db.String(191))
    status = db.Column(db.Boolean)
    minimum_low = db.Column(db.Integer)
    brand = db.Column(db.String(191))
    low_price = db.Column(db.DECIMAL(10, 2))
    stock_price = db.Column(db.DECIMAL(10, 2))
    price = db.Column(db.DECIMAL(10, 2))
    quantity = db.Column(db.Integer)
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    def __init__(self, name, description, category_product, image, barcode, status, minimum_low, brand, low_price, stock_price, price, quantity):
        self.name = name
        self.description = description
        self.category_product = category_product
        self.image = image
        self.barcode = barcode
        self.status = status
        self.minimum_low = minimum_low
        self.brand = brand
        self.low_price = low_price
        self.stock_price = stock_price
        self.price = price
        self.quantity = quantity


def init_db(app):
    with app.app_context():
        db.init_app(app)
        db.create_all()
        check_database_connection()


def check_database_connection():
    try:
        db.session.query(text('1')).from_statement(text('SELECT 1')).all()
        print('Database connection successful.')
    except Exception as e:
        print('Error connecting to the database:', str(e))
        # Handle the exception, such as exiting the application or showing an error message to the user
