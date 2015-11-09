import sys
import json
import re
import requests
import time
# try:
#	 from urllib.parse import urlparse
# except ImportError:
#	from urlparse import urlparse
import urllib.parse
try:
	from conf import *
	from exceptions import *
except SystemError:
	from .conf import *
	from .exceptions import *


dmm_token_pattern = re.compile(r'"DMM_TOKEN", "([\d|\w]+)"')
token_pattern = re.compile(r'"token": "([\d|\w]+)"')
osapi_url_pattern = re.compile('URL\W+:\W+"(.*)",')
regionFlags = {"ckcy": "1", "cklg": "welcome"}

def get_time():
	return int(time.time()*1000)


def get_dmm_tokens(s):
	req = s.get(DMM_LOGIN_URL, headers=headers, timeout=REQUESTS_TIMEOUT, cookies=regionFlags)
	m = dmm_token_pattern.search(req.text)
	if m is None:
		raise DmmTokenError
	else:
		dmm_token = m.group(1)
	m = token_pattern.search(req.text)
	if m is None:
		raise TokenError
	else:
		token = m.group(1)
	return dmm_token, token


def get_ajax_tokens(s, dmm_token, token):
	headers.update({'DMM_TOKEN': dmm_token,
					'Referer': DMM_LOGIN_URL,
					'X-Requested-With': 'XMLHttpRequest'})
	req = s.post(AJAX_TOKEN_URL, data={'token': token}, headers=headers, timeout=REQUESTS_TIMEOUT)
	try:
		j = json.loads(req.text)
		id_token = j['token']
		idKey = j['login_id']
		pwKey = j['password']
	except:
		raise AjaxRequestError
	return id_token, idKey, pwKey


def get_osapi(s, login_id, password, id_token, idKey, pwKey):
	del headers['DMM_TOKEN']
	del headers['X-Requested-With']
	post_data = {
		'login_id': login_id,
		'password': password,
		'token': id_token,
		idKey: login_id,
		pwKey: password,
		'save_login_id': '0',
		'save_password': '0',
		'path': '',
	}
	req = s.post(DMM_AUTH_URL, data=post_data, headers=headers, timeout=REQUESTS_TIMEOUT)
	#print(req.text)
	req = s.get(GAME_URL, headers=headers, timeout=REQUESTS_TIMEOUT, cookies=regionFlags)
	m = osapi_url_pattern.search(req.text)
	if m is None:
		raise LoginError
	else:
		osapi_url = m.group(1)
	return osapi_url


def parse_osapi_url(osapi_url):
	qs = urllib.parse.parse_qs(urllib.parse.urlparse(osapi_url).query)
	owner = int(qs['owner'][0])
	st = qs['st'][0]
	return owner, st


def get_world_id(s, owner, osapi_url):
	url = WORLD_URL % (owner, get_time())
	headers.update({'Referer': osapi_url})
	req = s.get(url, headers=headers, timeout=REQUESTS_TIMEOUT)
	svdata = json.loads(req.text[7:])
	if svdata['api_result'] == 1:
		world_id = svdata['api_data']['api_world_id']
		return world_id
	else:
		raise WorldIdError


def get_api_tokens(s, world_id, owner, st):
	url = FLASH_URL % (WORLD_IP[world_id-1], owner, get_time())
	post_data = {
		'url': url,
		'httpMethod': 'GET',
		'authz': 'signed',
		'st': st,
		'contentType': 'JSON',
		'numEntries': '3',
		'getSummaries': 'false',
		'signOwner': 'true',
		'signViewer': 'true',
		'gadget': 'http://203.104.209.7/gadget.xml',
		'container': 'dmm',
	}
	req = s.post(MAKE_REQUEST_URL, data=post_data, headers=headers, timeout=REQUESTS_TIMEOUT)
	rsp = json.loads(req.text[27:])
	if rsp[url]['rc'] != 200:
		raise ApiTokensError
	svdata = json.loads(rsp[url]['body'][7:])
	if svdata['api_result'] != 1:
		raise ApiTokensError
	api_token = svdata['api_token']
	api_starttime = svdata['api_starttime']
	return api_token, api_starttime


def get_osapi_url(login_id, password):
	s = requests.Session()
	dmm_token, token = get_dmm_tokens(s,)
	id_token, idKey, pwKey = get_ajax_tokens(s, dmm_token, token)
	osapi_url = get_osapi(s, login_id, password, id_token, idKey, pwKey)
	return osapi_url


def get_flash_url(login_id, password):
	s = requests.Session()
	dmm_token, token = get_dmm_tokens(s,)
	id_token, idKey, pwKey = get_ajax_tokens(s, dmm_token, token)
	osapi_url = get_osapi(s, login_id, password, id_token, idKey, pwKey)
	owner, st = parse_osapi_url(osapi_url)
	world_id = get_world_id(s, owner, osapi_url)
	world_ip = WORLD_IP[world_id-1]
	api_token, api_starttime = get_api_tokens(s, world_id, owner, st)
	flash_url = 'http://%s/kcs/mainD2.swf?api_token=%s&amp;api_starttime=%d' % \
				(world_ip, api_token, api_starttime)
	return flash_url, world_ip, api_token, api_starttime, owner


if __name__ == '__main__':

	login_id = sys.argv[1]
	password = sys.argv[2]
	try:
		flash_url, world_ip, api_token, api_starttime, owner = get_flash_url(login_id, password)
		result = {
			"status": "success",
			"serverip": world_ip,
			"token": api_token,
			"starttime": api_starttime,
			"owner": owner
		}
		print(json.dumps(result))
	except DmmTokenError:
		result = {
			"status": "Cannot Get DMM Token (服务器内部错误)"
		}
		print(json.dumps(result))
	except TokenError:
		result = {
			"status": "Cannot Get DMM Token (服务器内部错误)"
		}
		print(json.dumps(result))
	except AjaxRequestError:
		result = {
			"status": "Cannot Get Login Tokens Ajax (服务器内部错误)"
		}
		print(json.dumps(result))
	except LoginError:
		result = {
			"status": "登录失败: 用户名密码错误或者DMM要求您修改密码"
		}
		print(json.dumps(result))
	except OsapiUrlError:
		result = {
			"status": "Malformed OSAPI URL (服务器内部错误)"
		}
		print(json.dumps(result))
	except ApiTokensError:
		result = {
			"status": "Malformed response. Cannot get token info (服务器内部错误)"
		}
		print(json.dumps(result))
	except:
		result = {
			"status": "An unknown error occured. (服务器内部错误)"
		}
		print(json.dumps(result))

