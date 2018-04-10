# angular-utf8-base64

AngularJS service for UTF-8 and Base64 and Base64url Javascript Encoding.

CommonJS support wrapper for [the version by Andrey Bezyazychniy](https://github.com/stranger82/angular-utf8-base64). His implementation simply puts an AngularJS module wrapper around the [Really fast Javascript Base64 encoder/decoder with utf-8 support](http://jsbase64.codeplex.com/releases/view/89265), and this version wraps both to provide CommonJS support.


## Installation

### NPM

```sh
npm install angular-utf8-base64
```

```html
<script src="angular-utf8-base64.js"></script>
```

## Usage

```javascript
angular
    .module('someApp', ['utf8-base64'])
    .controller('someController', [

        '$scope', 'base64',
        function ($scope, base64) {

            $scope.encoded = base64.encode('a string');
            $scope.decoded = base64.decode('YSBzdHJpbmc=');
    }]);
```

### Base64Url Support

Commonly used for supporting JWS and JWT encodings, base64url encoding creates a URL safe output.

```javascript
angular
    .module('someApp', ['utf8-base64'])
    .controller('someController', [

        '$scope', 'base64',
        function ($scope, base64) {

            $scope.encoded = base64.urlencode('a string');
            $scope.decoded = base64.urldecode('YSBzdHJpbmc');
    }]);
```
