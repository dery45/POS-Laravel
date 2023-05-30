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
app.config['JWT_SECRET_KEY'] = 'suge-key'
# jwt = JWTManager(app)
api = Api(app)
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
check_database_connection()

class Category(db.Model):
    __tablename__ = 'categories'

    cat_id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(255), nullable=False)
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    def __init__(self, name):
        self.name = name

class CustomJSONEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, datetime):
            return obj.isoformat()
        return super().default(obj)
    
app.json_encoder = CustomJSONEncoder

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

class PriceHistory(db.Model):
    __tablename__ = 'price_history'

    price_id = db.Column(db.Integer, primary_key=True)
    fk_product_id = db.Column(db.Integer, nullable=False)
    normal_price = db.Column(db.Numeric(10, 2), nullable=False)
    low_price = db.Column(db.Numeric(10, 2), nullable=False)
    stock_price = db.Column(db.Numeric(10, 2), nullable=False)
    created_at = db.Column(db.DateTime, default=db.func.current_timestamp())

    def __init__(self, fk_product_id, normal_price, low_price, stock_price):
        self.fk_product_id = fk_product_id
        self.normal_price = normal_price
        self.low_price = low_price
        self.stock_price = stock_price

class StockHistory(db.Model):
    __tablename__ = 'stock_history'

    id = db.Column(db.Integer, primary_key=True)
    fk_product_id = db.Column(db.Integer, nullable=False)
    stock = db.Column(db.Integer, nullable=False)
    created_at = db.Column(db.DateTime, default=db.func.current_timestamp())

    def __init__(self, fk_product_id, stock):
        self.fk_product_id = fk_product_id
        self.stock = stock

class ProductResource(Resource):
    def post(self):
        data = request.get_json()
        name = data.get('name')
        description = data.get('description')
        fk_cat_id = data.get('fk_cat_id')
        image = data.get('image')
        status = data.get('status')
        minimum_low = data.get('minimum_low')
        brand = data.get('brand')
        stock = data.get('stock')
        normal_price = data.get('normal_price')
        low_price = data.get('low_price')
        stock_price = data.get('stock_price')

        # Create a new product instance
        product = Product(
            name=name,
            description=description,
            fk_cat_id=fk_cat_id,
            image=image,
            status=status,
            minimum_low=minimum_low,
            brand=brand,
            stock=stock,
            normal_price=normal_price,
            low_price=low_price,
            stock_price=stock_price
        )

        try:
            # Add the product to the database
            db.session.add(product)
            db.session.commit()

            #Insert the history table
            stock_history = StockHistory(
                fk_product_id = product.product_id,
                stock = product.stock
                )
            # Add the product to the history
            db.session.add(stock_history)
            db.session.commit()

            #Insert the history table
            price_history = PriceHistory(
                fk_product_id = product.product_id,
                normal_price = product.normal_price,
                low_price = product.low_price,
                stock_price = product.stock_price
                )
            # Add the product to the history
            db.session.add(price_history)
            db.session.commit()

            return {'message': 'Product created successfully', 'product_id': product.product_id}, 201
            
        except Exception as e:
            db.session.rollback()
            return {'message': 'Failed to create product', 'error': str(e)}, 500   

class GetAllProductsResource(Resource):
    def get(self):
        products = db.session.query(Product, Category).join(Category, Product.fk_cat_id == Category.cat_id).all()
        result = []
        for product, category in products:
            result.append({
                'product_id': product.product_id,
                'name': product.name,
                'description': product.description,
                'category_name': category.name,
                'image': product.image,
                'status': product.status,
                'minimum_low': product.minimum_low,
                'brand': product.brand,
                'stock': product.stock,
                'normal_price': str(product.normal_price),
                'low_price': str(product.low_price),
                'stock_price': str(product.stock_price),
                'created_at': product.created_at,
                'updated_at': product.updated_at
            })
        return jsonify(result)


class GetProductResource(Resource):
    def get(self, product_id):
        product = db.session.query(Product, Category).join(Category, Product.fk_cat_id == Category.cat_id).filter(Product.product_id == product_id).first()
        if product:
            product_data, category = product
            result = {
                'product_id': product_data.product_id,
                'name': product_data.name,
                'description': product_data.description,
                'category_name': category.name,
                'image': product_data.image,
                'status': product_data.status,
                'minimum_low': product_data.minimum_low,
                'brand': product_data.brand,
                'stock': product_data.stock,
                'normal_price': str(product_data.normal_price),
                'low_price': str(product_data.low_price),
                'stock_price': str(product_data.stock_price),
                'created_at': product_data.created_at,
                'updated_at': product_data.updated_at
            }
            return jsonify(result)
        else:
            return {'message': 'Product not found'}, 404

class PutProductResource(Resource):
    def put(self, product_id):
        product = Product.query.get(product_id)
        if not product:
            return {'message': 'Product not found'}, 404

        data = request.get_json()
        product.name = data.get('name', product.name)
        product.description = data.get('description', product.description)
        product.fk_cat_id = data.get('fk_cat_id', product.fk_cat_id)
        product.image = data.get('image', product.image)
        product.status = data.get('status', product.status)
        product.minimum_low = data.get('minimum_low', product.minimum_low)
        product.brand = data.get('brand', product.brand)
        product.stock = data.get('stock', product.stock)
        product.normal_price = data.get('normal_price', product.normal_price)
        product.low_price = data.get('low_price', product.low_price)
        product.stock_price = data.get('stock_price', product.stock_price)

        try:
            db.session.commit()
            #Insert the history table
            stock_history = StockHistory(
                fk_product_id = product.product_id,
                stock = product.stock
                )
            # Add the product to the history
            db.session.add(stock_history)
            db.session.commit()

            #Insert the history table
            price_history = PriceHistory(
                fk_product_id = product.product_id,
                normal_price = product.normal_price,
                low_price = product.low_price,
                stock_price = product.stock_price
                )
            # Add the product to the history
            db.session.add(price_history)
            db.session.commit()

            return {'message': 'Product updated successfully'}, 200

        except Exception as e:
            db.session.rollback()
            return {'message': 'Failed to update product', 'error': str(e)}, 500

class DeleteProductResource(Resource):
    def delete(self, product_id):
        product = Product.query.get(product_id)
        if not product:
            return {'message': 'Product not found'}, 404

        try:
            db.session.delete(product)
            db.session.commit()
            return {'message': 'Product deleted successfully'}, 200
        except Exception as e:
            db.session.rollback()
            return {'message': 'Failed to delete product', 'error': str(e)}, 500


api.add_resource(ProductResource, '/createproduct')
api.add_resource(GetAllProductsResource, '/products')
api.add_resource(GetProductResource, '/products/<int:product_id>')
api.add_resource(PutProductResource, '/products/<int:product_id>')
api.add_resource(DeleteProductResource, '/products/<int:product_id>')

if __name__ == '__main__':
    app.run()