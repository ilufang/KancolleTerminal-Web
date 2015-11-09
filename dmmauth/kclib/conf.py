DMM_LOGIN_URL = 'https://www.dmm.com/my/-/login/'
AJAX_TOKEN_URL = 'https://www.dmm.com/my/-/login/ajax-get-token/'
DMM_AUTH_URL = 'https://www.dmm.com/my/-/login/auth/'
GAME_URL = 'http://www.dmm.com/netgame/social/-/gadgets/=/app_id=854854/'
WORLD_URL = 'http://203.104.209.7/kcsapi/api_world/get_id/%d/1/%d'
FLASH_URL = 'http://%s/kcsapi/api_auth_member/dmmlogin/%d/1/%d'
MAKE_REQUEST_URL = 'http://osapi.dmm.com/gadgets/makeRequest'
WORLD_IP = (
    "203.104.209.71",
    "125.6.184.15",
    "125.6.184.16",
    "125.6.187.205",
    "125.6.187.229",
    "125.6.187.253",
    "125.6.188.25",
    "203.104.248.135",
    "125.6.189.7",
    "125.6.189.39",
    "125.6.189.71",
    "125.6.189.103",
    "125.6.189.135",
    "125.6.189.167",
    "125.6.189.215",
    "125.6.189.247",
    "203.104.209.23",
    "203.104.209.39",
    "203.104.209.55",
    "203.104.209.102",
)

REQUESTS_TIMEOUT = 30
REQUESTS_USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko'

SHIMAKAZEGO_PROXIES = {'http': 'http://127.0.0.1:8099', 'https': 'http://127.0.0.1:8099'}

headers = {
    'user-agent': REQUESTS_USER_AGENT,
}
