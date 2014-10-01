import requests
from requests.auth import HTTPBasicAuth

url = 'http://hrmservice/users'

payload = {'username': 'interface2', 'password': 'interface2password!', 'email_address' : 'interface2@mailinator.com'}
r = requests.post(url, data = payload, auth=HTTPBasicAuth('interface1', 'interface1'))
print (r.content)