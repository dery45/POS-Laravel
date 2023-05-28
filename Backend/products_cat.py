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

class GetAllCategoryResource(Resource):
    def get(self):
        categories = db.session.query(Category).all()

        data = []
        for cat in categories:
            cat_data = {
                'id': cat.cat_id,
                'name': cat.name,
                'created_at': cat.created_at,
                'updated_at': cat.updated_at
            }
            data.append(cat_data)
        return jsonify(data)

class GetCategoryResource(Resource):
    def get(self,cat_id):
        categories = db.session.query(Category).filter(Category.cat_id == cat_id).first()
        if categories:
            data = {
                'id': categories.cat_id,
                'name': categories.name,
                'created_at': categories.created_at,
                'updated_at': categories.updated_at
            }
            return jsonify(data)

        else:
            response = app.response_class(
                response=json.dumps({'message': 'Category not found'}),
                status=404,
                mimetype='application/json'
            )
            return response

class CreateCategoryResource(Resource):
    def post(self):
        name = request.json.get('name')
        category = Category(name = name)
        
        db.session.add(category)
        db.session.commit()

        return jsonify({'message': 'Category created successfully'})

class UpdateCategoryResource(Resource):
    def put(self, cat_id):
        # Retrieve the user from the database by ID
        category = Category.query.get(cat_id)
        if not category:
            response = app.response_class(
                response=json.dumps({'message': 'Category not found'}),
                status=404,
                mimetype='application/json'
            )
            return response
        
        updated_data = request.json

        category.name = updated_data.get('name')
        db.session.commit()

        updated_category = Category.query.get(cat_id)
        
        data = {
            'id': updated_category.cat_id,
            'name': updated_category.name,
            'created_at': updated_category.created_at,
            'updated_at': updated_category.updated_at
        }
        return jsonify(data)

class DeleteCategoryResource(Resource):
    def delete(self,cat_id):
        category = Category.query.get(cat_id)
        if category:
            db.session.delete(category)
            db.session.commit()
            return jsonify({'message': 'Category deleted successfully'})
        
        else:
            return jsonify({'message': 'Category not found'})

api = Api(app)
api.add_resource(GetAllCategoryResource, '/categories')
api.add_resource(GetCategoryResource, '/categories/<int:cat_id>')
api.add_resource(UpdateCategoryResource, '/categories/<int:cat_id>')
api.add_resource(CreateCategoryResource, '/createcategories')
api.add_resource(DeleteCategoryResource, '/categories/<int:cat_id>')

if __name__ == '__main__':
    app.run()
