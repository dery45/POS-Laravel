from subprocess import Popen

def run_flask_app(app_file, port):
    cmd = ['waitress-serve', f'--port={port}', app_file]
    return Popen(cmd)

if __name__ == '__main__':
    rekap_harian = run_flask_app('rekap_harian:app', 5550)


    # Wait for processes to complete
    rekap_harian.wait()
