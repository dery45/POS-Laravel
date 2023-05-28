from flask import Flask, request, jsonify
from flask_restful import Resource, Api
from flask_cors import CORS
from flask_sqlalchemy import SQLAlchemy
# from flask_jwt_extended import JWTManager, jwt_required, get_jwt_identity
from sqlalchemy import text
import json

app = Flask(__name__)
app.run(port=5555)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:@localhost:3306/pos_suge'
app.config['JWT_SECRET_KEY'] = 'suge-key'
# jwt = JWTManager(app)
api = Api(app)
db = SQLAlchemy(app)
CORS(app)

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

    def __init__(self, username, name, address, phone_number, fk_role_id, password, email):
        self.username = username
        self.name = name
        self.address = address
        self.phone_number = phone_number
        self.fk_role_id = fk_role_id
        self.password = password
        self.email = email

class Role(db.Model):
    __tablename__ = 'roles'

    role_id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(255), nullable=False)
    access = db.Column(db.String(255), nullable=False)

    def __init__(self, name, access):
        self.name = name
        self.access = access

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

# Endpoint to create a new user
class CreateUserResource(Resource):
    def post(self):
        username = request.json.get('username')
        name = request.json.get('name')
        address = request.json.get('address')
        phone_number = request.json.get('phone_number')
        fk_role_id = request.json.get('fk_role_id')
        password = request.json.get('password')
        email = request.json.get('email')

        # Create a new user instance
        user = User(username=username, name=name, address=address, phone_number=phone_number,
                    fk_role_id=fk_role_id, password=password, email=email)

        # Add the user to the database
        db.session.add(user)
        db.session.commit()

        return jsonify({'message': 'User created successfully'})

# Endpoint to get user details by ID
class GetUserResource(Resource):
    def get(self, user_id):
        # Retrieve the user from the database by ID
        user = db.session.query(User, Role).join(Role).filter(User.id == user_id).first()

        if user:
            user_obj, role_obj = user
            data = {
                'id': user_obj.id,
                'name': user_obj.name,
                'username':user_obj.username,
                'address': user_obj.address,
                'phone_number': user_obj.phone_number,
                'email':user_obj.email,
                'role': {
                    'id': role_obj.role_id,
                    'name': role_obj.name,
                    'access': role_obj.access
                }
            }
            response = app.response_class(
                response=json.dumps(data),
                status=200,
                mimetype='application/json'
            )
            return response
        else:
            response = app.response_class(
                response=json.dumps({'message': 'User not found'}),
                status=404,
                mimetype='application/json'
            )
            return response

# Endpoint to update user details by ID
class UpdateUserResource(Resource):
    def put(self, user_id):
        # Retrieve the user from the database by ID
        user = User.query.get(user_id)
        if not user:
            response = app.response_class(
                response=json.dumps({'message': 'User not found'}),
                status=404,
                mimetype='application/json'
            )
            return response

        # Retrieve the updated data from the request
        updated_data = request.json

        # Update the user object with the new data
        user.name = updated_data.get('name')
        user.address = updated_data.get('address')
        user.phone_number = updated_data.get('phone_number')
        user.fk_role_id = updated_data.get('fk_role_id')
        user.email = updated_data.get('email')
        user.password = updated_data.get('password')

        # Save the changes to the database
        db.session.commit()

        # Prepare the response
        user = db.session.query(User, Role).join(Role).filter(User.id == user_id).first()
        user_obj, role_obj = user
        data = {
                'id': user_obj.id,
                'name': user_obj.name,
                'username':user_obj.username,
                'address': user_obj.address,
                'phone_number': user_obj.phone_number,
                'email':user_obj.email,
                'role': {
                    'id': role_obj.role_id,
                    'name': role_obj.name,
                    'access': role_obj.access
                }
            }
        response = app.response_class(
                response=json.dumps(data),
                status=200,
                mimetype='application/json'
            )
        return response

# Endpoint to delete a user by ID
class DeleteUserResource(Resource):
    def delete(self, user_id):
        # Retrieve the user from the database by ID
        user = User.query.get(user_id)
        if user:
            # Delete the user from the database
            db.session.delete(user)
            db.session.commit()

            return jsonify({'message': 'User deleted successfully'})
        else:
            return jsonify({'error': 'User not found'}), 404

# Endpoint to retrive all users
class GetAllUsersResource(Resource):
    def get(self):
        # Retrieve all users and their corresponding roles from the database
        users = db.session.query(User, Role).join(Role).all()

        # Prepare the response data
        data = []
        for user, role in users:
            user_data = {
                'id': user.id,
                'username': user.username,
                'name': user.name,
                'address': user.address,
                'phone_number': user.phone_number,
                'email': user.email,
                'role': {
                    'id': role.role_id,
                    'name': role.name,
                    'access': role.access
                }
            }
            data.append(user_data)

        # Return the response with the data
        response = app.response_class(
            response=json.dumps(data),
            status=200,
            mimetype='application/json'
        )
        return response

# Add the resources to the API
api.add_resource(CreateUserResource, '/users')
api.add_resource(GetAllUsersResource, '/allusers')
api.add_resource(GetUserResource, '/users/<int:user_id>')
api.add_resource(UpdateUserResource, '/users/<int:user_id>')
api.add_resource(DeleteUserResource, '/users/<int:user_id>')

if __name__ == '__main__':
    app.run()
