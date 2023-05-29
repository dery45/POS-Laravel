from flask import Flask, request, jsonify
from json import JSONEncoder
from flask_restful import Resource, Api
from flask_cors import CORS
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import text
import json
from datetime import datetime

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:@localhost:3306/pos_suge'
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

class Product(db.Model):
    __tablename__ = 'products'

    product_id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(255))
    description = db.Column(db.Text)
    fk_cat_id = db.Column(db.Integer)
    image = db.Column(db.String(255))
    status = db.Column(db.String(50))
    minimum_low = db.Column(db.Numeric(10, 2))
    brand = db.Column(db.String(100))
    created_at = db.Column(db.DateTime, default=db.func.current_timestamp(), onupdate=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, onupdate=db.func.current_timestamp())
    stock = db.Column(db.Integer)
    normal_price = db.Column(db.Numeric(10, 2))
    low_price = db.Column(db.Numeric(10, 2))
    stock_price = db.Column(db.Numeric(10, 2))

    def __init__(self, name, description, fk_cat_id, image, status, minimum_low, brand, stock, normal_price, low_price, stock_price):
        self.name = name
        self.description = description
        self.fk_cat_id = fk_cat_id
        self.image = image
        self.status = status
        self.minimum_low = minimum_low
        self.brand = brand
        self.stock = stock
        self.normal_price = normal_price
        self.low_price = low_price
        self.stock_price = stock_price

# Define the stock history model
class StockHistory(db.Model):
    __tablename__ = 'stock_history'

    id = db.Column(db.Integer, primary_key=True)
    fk_product_id = db.Column(db.Integer, nullable=False)
    stock = db.Column(db.Integer, nullable=False)
    created_at = db.Column(db.DateTime, default=db.func.current_timestamp())

    def __init__(self, fk_product_id, stock):
        self.fk_product_id = fk_product_id
        self.stock = stock

# Create an API resource for retrieving stock history by product_id
class StockHistoryResource(Resource):
    def get(self):
        data = request.get_json()
        fk_product_id = data.get('fk_product_id')
        if not fk_product_id:
            return {'message': 'Product ID Not Found'}, 400

        stock_history = db.session.query(
            StockHistory.id,
            StockHistory.fk_product_id,
            StockHistory.stock,
            StockHistory.created_at,
            Product.name,
        ).join(
            Product, StockHistory.fk_product_id == Product.product_id
        ).filter(
            StockHistory.fk_product_id == fk_product_id
        ).all()

        result = []
        for entry in stock_history:
            result.append({
                'id': entry.id,
                'fk_product_id': entry.fk_product_id,
                'stock': entry.stock,
                'created_at': entry.created_at,
                'name': entry.name
            })
        return jsonify(result)

api = Api(app)
api.add_resource(StockHistoryResource, '/stockhistory/')

if __name__ == '__main__':
    app.run()