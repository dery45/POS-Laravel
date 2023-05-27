from flask import Flask, request, jsonify
from flask_cors import CORS
from flask_sqlalchemy import SQLAlchemy
from flask_jwt_extended import JWTManager, jwt_required, get_jwt_identity, create_access_token
from sqlalchemy import text

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:@localhost:3306/pos_suge'
app.config['JWT_SECRET_KEY'] = 'suge-key'
jwt = JWTManager(app)
db = SQLAlchemy(app)
CORS(app)

# Define your database models here
# # Example:
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

@app.route('/login', methods=['POST'])
def login():
    # Retrieve the name and password from the request
    username = request.json.get('username')
    password = request.json.get('password')

    # Fetch the user from the database
    user = User.query.filter_by(username=username).first()

    # Check if the user exists and the password is correct
    if user and user.password == password:
        # Authentication successful
        # Generate and return a JWT token
        token = generate_jwt_token(user.username)
        return jsonify({'token': token}), 200
    else:
        # Authentication failed
        return jsonify({'message': 'Invalid credentials'}), 401


# Helper function to generate a JWT token
def generate_jwt_token(name):
    # Add your JWT token generation logic here
    # Example: Using flask_jwt_extended package
    token = create_access_token(identity=name)
    return token


# Example protected API endpoint
@app.route('/protected', methods=['GET'])
@jwt_required()
def protected_endpoint():
    # Access the current user's identity using get_jwt_identity()
    current_user = get_jwt_identity()
    return jsonify({'message': 'Protected endpoint', 'user': current_user}), 200

@app.route('/all', methods=['GET'])
def free():
    # Access the current user's identity using get_jwt_identity()
    return jsonify({'message': 'Not Protected endpoint', 'user': 'USERNAME'}), 200


if __name__ == '__main__':
    app.run()