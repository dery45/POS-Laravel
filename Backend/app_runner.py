import subprocess
import os
import webbrowser
import time

# Command 1
directory1 = r"D:\Work Area\Project\XAMPP"
command1 = 'xampp_start.exe'
os.chdir(directory1)
subprocess.call(command1, shell=True)

# Command 2
<<<<<<< HEAD
directory2 = r'C:\xampp\htdocs\POS-Laravel'
=======
directory2 = r'D:\Work Area\Project\POS - Github\laravel-pos-master'
>>>>>>> 825defc763cef8f8bd19bb0e9df3f98c94946ef2
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
directory4 = r"D:\Work Area\Project\POS - Github\laravel-pos-master\Backend"
>>>>>>> 825defc763cef8f8bd19bb0e9df3f98c94946ef2
command4 = 'activate.bat && cd ../.. && waitress-serve --port=5550 rekap_harian:app'
os.chdir(directory4)
subprocess.Popen(['cmd', '/k', command4], shell=True)

# Command 5
<<<<<<< HEAD
directory5 = r"C:\xampp\htdocs\POS-Laravel\Backend\venv\Scripts"
=======
directory5 = r"D:\Work Area\Project\POS - Github\laravel-pos-master\Backend"
>>>>>>> 825defc763cef8f8bd19bb0e9df3f98c94946ef2
command5 = 'activate.bat && cd ../.. && waitress-serve --port=5551 dashboard:app'
os.chdir(directory5)
subprocess.Popen(['cmd', '/k', command5], shell=True)

# Wait for server to start
time.sleep(5)

# Open browser
url = 'http://127.0.0.1:8000/login'
webbrowser.open(url)