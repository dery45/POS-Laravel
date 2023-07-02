import subprocess
import os
import webbrowser
import time

# Command 1
<<<<<<< HEAD
directory1 = r"C:\xampp"
=======
directory1 = r"C:\XAMPP"
>>>>>>> 756270cc04c979fb4590fc9deb4dc382e5613da8
command1 = 'xampp_start.exe'
os.chdir(directory1)
subprocess.call(command1, shell=True)

# Command 2
<<<<<<< HEAD
directory2 = r'C:\xampp\htdocs\POS-Laravel'
=======
directory2 = r'C:\xampp\htdocs\pos-project\POS-Laravel'
>>>>>>> 756270cc04c979fb4590fc9deb4dc382e5613da8
command2 = 'npm run dev'
os.chdir(directory2)
subprocess.Popen(['cmd', '/k', command2], shell=True)

# Command 3
command3 = 'php artisan serve'
subprocess.Popen(['cmd', '/k', command3], shell=True)

# Command 4
<<<<<<< HEAD
directory4 = r"C:\xampp\htdocs\POS-Laravel\Backend\venv\Scripts"
=======
directory4 = r"C:\xampp\htdocs\pos-project\POS-Laravel\Backend"
>>>>>>> 756270cc04c979fb4590fc9deb4dc382e5613da8
command4 = 'activate.bat && cd ../.. && waitress-serve --port=5550 rekap_harian:app'
os.chdir(directory4)
subprocess.Popen(['cmd', '/k', command4], shell=True)

# Command 5
<<<<<<< HEAD
directory5 = r"C:\xampp\htdocs\POS-Laravel\Backend\venv\Scripts"
=======
directory5 = r"C:\xampp\htdocs\pos-project\POS-Laravel\Backend"
>>>>>>> 756270cc04c979fb4590fc9deb4dc382e5613da8
command5 = 'activate.bat && cd ../.. && waitress-serve --port=5551 dashboard:app'
os.chdir(directory5)
subprocess.Popen(['cmd', '/k', command5], shell=True)

# Wait for server to start
time.sleep(5)

# Open browser
url = 'http://127.0.0.1:8000/login'
webbrowser.open(url)