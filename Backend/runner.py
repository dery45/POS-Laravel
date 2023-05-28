from subprocess import Popen

def run_flask_app(app_file, port):
    cmd = ['waitress-serve', f'--port={port}', app_file]
    return Popen(cmd)

if __name__ == '__main__':
    users_app_process = run_flask_app('users:app', 5550)
    products_cat_app_process = run_flask_app('products_cat:app', 5551)
    products_app_process = run_flask_app('products:app', 5552)

    # Add any additional logic or tasks here

    # Wait for both processes to complete
    users_app_process.wait()
    products_cat_app_process.wait()
