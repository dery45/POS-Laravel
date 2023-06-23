from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from datetime import datetime

# Create the Flask application
app = Flask(__name__)

# Configure the database connection
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:@localhost:3306/laravel'
# app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

# Create the SQLAlchemy database instance
db = SQLAlchemy(app)


# Define the Product model
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

    def __init__(self, name, description, category_product, image, barcode, status, minimum_low, brand, low_price,
                 stock_price, price, quantity):
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

class PriceHistory(db.Model):
    __tablename__ = 'price_histories'

    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    low_price = db.Column(db.DECIMAL(10, 2))
    stock_price = db.Column(db.DECIMAL(10, 2))
    price = db.Column(db.DECIMAL(10, 2))
    fk_product_id = db.Column(db.BigInteger, db.ForeignKey('products.id'))
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    def __init__(self, low_price, stock_price, price, fk_product_id):
        self.low_price = low_price
        self.stock_price = stock_price
        self.price = price
        self.fk_product_id = fk_product_id

# Define the StockHistory model
class StockHistory(db.Model):
    __tablename__ = 'stock_history'

    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    fk_product_id = db.Column(db.BigInteger, db.ForeignKey('products.id'))
    quantity = db.Column(db.Integer)
    created_at = db.Column(db.DateTime, nullable=False, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, nullable=True, onupdate=db.func.current_timestamp())

    def __init__(self, fk_product_id, quantity):
        self.fk_product_id = fk_product_id
        self.quantity = quantity


if __name__ == '__main__':
    # Enter the application context
    with app.app_context():
        # Retrieve the products from the products table
        products = Product.query.all()

        # Iterate over the products and create stock history records
        for product in products:
            stock_history = StockHistory(fk_product_id=product.id, quantity=product.quantity)
            db.session.add(stock_history)

        # Commit the changes to the database
        db.session.commit()

        # Retrieve the products from the products table
        products = Product.query.all()

        # Iterate over the products and create price history records
        for product in products:
            price_history = PriceHistory(
                low_price=product.low_price,
                stock_price=product.stock_price,
                price=product.price,
                fk_product_id=product.id,
            )
            db.session.add(price_history)

        # Commit the changes to the database
        db.session.commit()