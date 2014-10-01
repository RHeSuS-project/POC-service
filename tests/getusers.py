import requests
from requests.auth import HTTPBasicAuth

url = 'http://hrmservice/users'

payload = {'username': 'interface2', 'password': 'interface2password!', 'email_address' : 'interface2@mailinator.com'}
r = requests.get(url, auth=HTTPBasicAuth('interfacqdsfqdsf', 'interface1passwo'))
print (r.content)