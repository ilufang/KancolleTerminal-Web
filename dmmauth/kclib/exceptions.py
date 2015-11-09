class KcBaseException(Exception):
    pass


class DmmTokenError(KcBaseException):
    pass


class TokenError(KcBaseException):
    pass


class AjaxRequestError(KcBaseException):
    pass


class LoginError(KcBaseException):
    pass


class OsapiUrlError(KcBaseException):
    pass


class WorldIdError(KcBaseException):
    pass


class ApiTokensError(KcBaseException):
    pass
