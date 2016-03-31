{
	"_comment": "IMPORTANT : go to the wiki page to know about options configuration https://github.com/simogeo/Filemanager/wiki/Filemanager-configuration-file",
    "options": {
        "culture": "zh-cn",
        "lang": "php",
        "theme": "flat-dark",
        "defaultViewMode": "grid",
        "autoload": true,
        "showFullPath": false,
        "showTitleAttr": false,
        "browseOnly": false,
        "showConfirmation": true,
        "showThumbs": true,
        "generateThumbnails": true,
        "searchBox": true,
        "listFiles": true,
        "fileSorting": "default",
        "chars_only_latin": false,
        "dateFormat": "d M Y H:i",
        "serverRoot": false,
        "fileRoot": "/var/www/kancolle/kcres/files/",
        "baseUrl": false,
        "logger": false,
        "capabilities": ["select", "download", "rename", "delete", "replace"],
        "plugins": []
    },
    "security": {
        "allowFolderDownload": true,
        "allowChangeExtensions": true,
        "allowNoExtension": true,
        "uploadPolicy": "ALLOW_ALL",
        "uploadRestrictions": []
    },
    "upload": {
        "multiple": true,
        "number": 20,
        "overwrite": true,
        "imagesOnly": false,
        "fileSizeLimit": 64
    },
    "exclude": {
        "unallowed_files": [
            ".htaccess",
            "web.config"
        ],
        "unallowed_dirs": [
        ],
        "unallowed_files_REGEXP": "/.php/",
        "unallowed_dirs_REGEXP": "/^\\./"
    },
    "images": {
        "imagesExt": [
            "jpg",
            "jpe",
            "jpeg",
            "gif",
            "png",
            "svg"
        ],
        "resize": {
        	"enabled":false,
        	"maxWidth": 1280,
            "maxHeight": 1024
        }
    },
    "videos": {
        "showVideoPlayer": true,
        "videosExt": [
            "ogv",
            "mp4",
            "webm",
            "m4v"
        ],
        "videosPlayerWidth": 400,
        "videosPlayerHeight": 222
    },
    "audios": {
        "showAudioPlayer": true,
        "audiosExt": [
            "ogg",
            "mp3",
            "wav"
        ]
    },
    "pdfs": {
        "showPdfReader": true,
        "pdfsExt": [
            "pdf",
            "odp"
        ],
	    "pdfsReaderWidth": "640",
        "pdfsReaderHeight": "480"
    },
    "edit": {
        "enabled": true,
        "lineNumbers": true,
        "lineWrapping": true,
        "codeHighlight": false,
        "theme": "elegant",
        "editExt": [
            "txt",
            "csv",
            "json"
        ]
    },
    "customScrollbar": {
    	"enabled": true,
    	"theme": "inset-2-dark",
    	"button": true
    },
    "extras": {
        "extra_js": [],
        "extra_js_async": true
    },
    "icons": {
        "path": "images/fileicons/",
        "directory": "_Open.png",
        "default": "default.png"
    },
    "url": "https://github.com/simogeo/Filemanager",
    "version": "2.0.0-dev"
}
