from flask import Flask
from flask_restful import Resource, Api
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

api = Api(app)

class HelloWorld(Resource):
    def get(self):
        data = {'testing': 
                {'nama':'tono',
                 'absen':'32'}}
        return data

api.add_resource(HelloWorld, '/')

if __name__ == '__main__':
    app.run(debug=True)