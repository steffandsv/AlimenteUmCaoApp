angular.module("gmap_test.services", [])
// TODO: --|---- directive
	
// TODO: --|-------- sound-touch
.directive("soundTouch", function(){
	/** required: cordova-plugin-velda-devicefeedback **/
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("touchend", onTouchEnd);
			function onTouchEnd(event)
			{
				if (window.plugins && window.plugins.deviceFeedback){
					window.plugins.deviceFeedback.acoustic();
				}
			};
		}
	};
})
	
// TODO: --|-------- pdf-reader
.directive("pdfReader", [ "$window", function($window){
	var renderTask = null;
	var pdfLoaderTask = null;
	var debug = true;
	
	var backingScale = function(canvas) {
		var ctx = canvas.getContext("2d");
		var dpr = window.devicePixelRatio || 1;
		var bsr = ctx.webkitBackingStorePixelRatio ||
		ctx.mozBackingStorePixelRatio ||
		ctx.msBackingStorePixelRatio ||
		ctx.oBackingStorePixelRatio ||
		ctx.backingStorePixelRatio || 1;
		
		return dpr / bsr;
	};
	
	var setCanvasDimensions = function(canvas, w, h) {
		var ratio = backingScale(canvas);
		canvas.width = Math.floor(w * ratio);
		canvas.height = Math.floor(h * ratio);
		canvas.style.width = Math.floor(w) + "px";
		canvas.style.height = Math.floor(h) + "px";
		canvas.getContext("2d").setTransform(ratio, 0, 0, ratio, 0, 0);
		return canvas;
	};
	
	return {
		restrict: "E",
		templateUrl: function(element, attr) {
			return attr.templateUrl ? attr.templateUrl : "lib/pdf/pdf-viewer.html";
		},
		link: function(scope, element, attrs) {
			element.css("display", "block");
			var url = scope.pdfUrl = attrs.url;
			
			var httpHeaders = scope.httpHeaders;
			var pdfDoc = null;
			var pageToDisplay = isFinite(attrs.page) ? parseInt(attrs.page) : 1;
			var pageFit = attrs.scale === "page-fit";
			var scale = attrs.scale > 0 ? attrs.scale : 1;
			var canvasid = attrs.canvasid || "pdf-canvas";
			var canvas = document.getElementById(canvasid);
			
			debug = attrs.hasOwnProperty("debug") ? attrs.debug : false;
			var creds = attrs.usecredentials;
			var ctx = canvas.getContext("2d");
			var windowEl = angular.element($window);
		
			windowEl.on("scroll", function() {
				scope.$apply(function() {
					scope.scroll = windowEl[0].scrollY;
				});
			});
			
			PDFJS.disableWorker = true;
			scope.pageNum = pageToDisplay;
			
			scope.renderPage = function(num) {
				if (renderTask) {
					renderTask._internalRenderTask.cancel();
				}
					
				pdfDoc.getPage(num).then(function(page) {
					var viewport;
					var pageWidthScale;
					var renderContext;
					
					if (pageFit) {
						viewport = page.getViewport(1);
						var clientRect = element[0].getBoundingClientRect();
						pageWidthScale = clientRect.width / viewport.width;
						scale = pageWidthScale;
					}
					viewport = page.getViewport(scale);
					
					setCanvasDimensions(canvas, viewport.width, viewport.height);
					
					renderContext = {
						canvasContext: ctx,
						viewport: viewport
					};
					
					renderTask = page.render(renderContext);
					renderTask.promise.then(function() {
						if (typeof scope.pdfPageRender === "function") {
							scope.pdfPageRender();
						}
					}).catch(function(reason){
						if (typeof scope.pdfErrorRender === "function") {
							scope.pdfErrorRender(reason);
						}
					});
				});
			};
				
			scope.pdfPrevious = function() {
				if (scope.pageToDisplay <= 1) {
					return;
				}
				scope.pageToDisplay = parseInt(scope.pageToDisplay) - 1;
				scope.pageNum = scope.pageToDisplay;
			};
				
			scope.pdfNext = function() {
				if (scope.pageToDisplay >= pdfDoc.numPages) {
					return;
				}
				scope.pageToDisplay = parseInt(scope.pageToDisplay) + 1;
				scope.pageNum = scope.pageToDisplay;
			};
				
			scope.pdfZoomIn = function() {
				pageFit = false;
				scale = parseFloat(scale) + 0.2;
				scope.renderPage(scope.pageToDisplay);
				return scale;
			};
				
			scope.pdfZoomOut = function() {
				pageFit = false;
				scale = parseFloat(scale) - 0.2;
				scope.renderPage(scope.pageToDisplay);
				return scale;
			};
			
			scope.pdfFit = function() {
				pageFit = true;
				scope.renderPage(scope.pageToDisplay);
			}
			
			scope.changePage = function() {
				scope.renderPage(scope.pageToDisplay);
			};
			
			scope.pdfRotate = function() {
				if (canvas.getAttribute("class") === "rotate0") {
					canvas.setAttribute("class", "rotate90");
				} else if (canvas.getAttribute("class") === "rotate90") {
					canvas.setAttribute("class", "rotate180");
				} else if (canvas.getAttribute("class") === "rotate180") {
					canvas.setAttribute("class", "rotate270");
				} else {
					canvas.setAttribute("class", "rotate0");
				}
			};
			
			function clearCanvas() {
				if (ctx) {
					ctx.clearRect(0, 0, canvas.width, canvas.height);
				}
			}
			
			function renderPDF() {
				clearCanvas();
				
				var params = {
					"url": url,
					"withCredentials": creds
				};
				
				if (httpHeaders) {
					params.httpHeaders = httpHeaders;
				}
				
				if (url && url.length) {
					pdfLoaderTask = PDFJS.getDocument(params, null, null, scope.pdfProgress);
					pdfLoaderTask.then(function(_pdfDoc) {
							if (typeof scope.pdfLoad === "function") {
								scope.pdfLoad();
							}
							
							pdfDoc = _pdfDoc;
							scope.renderPage(scope.pageToDisplay);
							scope.$apply(function() {
								scope.pageCount = _pdfDoc.numPages;
							});
						},function(error) {
							if (error) {
								if (typeof scope.pdfError === "function") {
									scope.pdfError(error);
								}
							}
						}
					);
				}
			}
			
			scope.$watch("pageNum", function(newVal) {
				scope.pageToDisplay = parseInt(newVal);
				if (pdfDoc !== null) {
					scope.renderPage(scope.pageToDisplay);
				}
			});
			
			scope.$watch("pdfUrl", function(newVal) {
				if (newVal !== "") {
					if (debug) {
						console.log("pdfUrl value change detected: ", scope.pdfUrl);
					}
					url = newVal;
					scope.pageNum = scope.pageToDisplay = pageToDisplay;
					if (pdfLoaderTask) {
						pdfLoaderTask.destroy().then(function () {
							renderPDF();
						});
					} else {
						renderPDF();
					}
				}
			});
			
		}
	};
}])
// TODO: --|-------- zoomTap
.directive("zoomTap", function($compile, $ionicGesture) {
	return {
		link: function($scope, $element, $attrs) {
			var zoom = minZoom = 10;
			var maxZoom = 50;
			$element.attr("style", "width:" + (zoom * 10) + "%");
			var handlePinch = function(e){
				if (e.gesture.scale <= 1) {
					zoom--;
				}else{
					zoom++;
				}
				if (zoom >= maxZoom) {
					zoom = maxZoom;
				}
				if (zoom <= minZoom) {
					zoom = minZoom;
				}
				$element.attr("style", "width:" + (zoom * 10) + "%");
			};
			var handleDoubleTap = function(e){
				zoom++;
				if (zoom == maxZoom) {
					zoom = minZoom;
				}
				$element.attr("style", "width:" + (zoom * 10) + "%");
			};
			var pinchGesture = $ionicGesture.on("pinch", handlePinch, $element);
			var doubletapGesture = $ionicGesture.on("doubletap", handleDoubleTap, $element);
			$scope.$on("$destroy", function() {
				$ionicGesture.off(pinchGesture, "pinch", $element);
				$ionicGesture.off(doubletapGesture, "doubletap", $element);
			});
		}
	};
})
// TODO: --|-------- zoom-view
.directive("zoomView", function($compile,$ionicModal, $ionicPlatform){
	return {
			link: function link($scope, $element, $attrs){
				
				if(typeof $scope.zoomImages == "undefined"){
					$scope.zoomImages=0;
				}
				if(typeof $scope.imagesZoomSrc == "undefined"){
					$scope.imagesZoomSrc = {};
				}
				$scope.zoomImages++;
				var indeks = $scope.zoomImages;
				$scope.imagesZoomSrc[indeks] = $attrs.zoomSrc;
				
				$element.attr("ng-click", "showZoomView(" + indeks + ")");
				$element.removeAttr("zoom-view");
				$compile($element)($scope);
				$ionicPlatform.ready(function(){
					var zoomViewTemplate = "";
					zoomViewTemplate += "<ion-modal-view class=\"zoom-view\">";
					zoomViewTemplate += "<ion-header-bar class=\"bar bar-header light bar-balanced-900\">";
					zoomViewTemplate += "<div class=\"header-item title\"></div>";
					zoomViewTemplate += "<div class=\"buttons buttons-right header-item\"><span class=\"right-buttons\"><button ng-click=\"closeZoomView()\" class=\"button button-icon ion-close button-clear button-dark\"></button></span></div>";
					zoomViewTemplate += "</ion-header-bar>";
					zoomViewTemplate += "<ion-content overflow-scroll=\"true\">";
					zoomViewTemplate += "<ion-scroll zooming=\"true\" overflow-scroll=\"false\" direction=\"xy\" style=\"width:100%;height:100%;position:absolute;top:0;bottom:0;left:0;right:0;\">";
					zoomViewTemplate += "<img ng-src=\"{{ zoom_src }}\" style=\"width:100%!important;display:block;width:100%;height:auto;max-width:400px;max-height:700px;margin:auto;padding:10px;\"/>";
					zoomViewTemplate += "</ion-scroll>";
					zoomViewTemplate += "</ion-content>";
					zoomViewTemplate += "</ion-modal-view>";
					$scope.zoomViewModal = $ionicModal.fromTemplate(zoomViewTemplate,{
						scope: $scope,
						animation: "slide-in-up"
					});
					
					$scope.showZoomView = function(indeks){
						$scope.zoom_src = $scope.imagesZoomSrc[indeks] || $attrs.zoomSrc ;
						console.log(indeks,$scope.zoom_src,$scope.imagesZoomSrc);
						$scope.zoomViewModal.show();
					};
					$scope.closeZoomView= function(){
						$scope.zoomViewModal.hide();
					};
				});
		}
	};
})
				
// TODO: --|-------- headerShrink
.directive("headerShrink", function($document){
	var fadeAmt;
	var shrink = function(header, content, amt, max){
		amt = Math.min(44, amt);
		fadeAmt = 1 - amt / 44;
		ionic.requestAnimationFrame(function(){
			var translate3d = "translate3d(0, -" + amt + "px, 0)";
			if(header==null){return;}
			for (var i = 0, j = header.children.length; i < j; i++){
			header.children[i].style.opacity = fadeAmt;
				header.children[i].style[ionic.CSS.TRANSFORM] = translate3d;
			}
		});
	};
	return {
		link: function($scope, $element, $attr){
			var starty = $scope.$eval($attr.headerShrink) || 0;
			var shrinkAmt;
			var header = $document[0].body.querySelector(".navbar-title");
			var headerHeight = $attr.offsetHeight || 44;
			$element.bind("scroll", function(e){
				var scrollTop = null;
				if (e.detail){
					scrollTop = e.detail.scrollTop;
				} else if (e.target){
					scrollTop = e.target.scrollTop;
				}
				if (scrollTop > starty){
					shrinkAmt = headerHeight - Math.max(0, (starty + headerHeight) - scrollTop);
					shrink(header, $element[0], shrinkAmt, headerHeight);
				}else{
					shrink(header, $element[0], 0, headerHeight);
				}
			});
			$scope.$parent.$on("$ionicView.leave", function (){
				shrink(header, $element[0], 0, headerHeight);
			});
			$scope.$parent.$on("$ionicView.enter", function (){
				shrink(header, $element[0], 0, headerHeight);
			});
		}
	}
})
// TODO: --|-------- fileread
.directive("fileread",function($ionicLoading,$timeout){
	return {
		scope:{
			fileread: "="
		},
		link: function(scope, element,attributes){
			element.bind("change", function(changeEvent) {
				$ionicLoading.show({
					template: '<div class="loader"><svg class="circular"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div>'
				});
				scope.fileread = "";
				var reader = new FileReader();
				reader.onload = function(loadEvent) {
					try{
						scope.$apply(function(){
							scope.fileread = loadEvent.target.result;
						});
					}catch(err){
						//alert(err.message);
					}
				}
				reader.onloadend = function(loadEvent) {
					try{
						$timeout(function(){
							$ionicLoading.hide();
								scope.fileread = loadEvent.target.result;
						},300);
					}catch(err){
						//alert(err.message);
					}
				}
				if(changeEvent.target.files[0]){
					reader.readAsDataURL(changeEvent.target.files[0]);
				}
				$timeout(function(){
					$ionicLoading.hide();
				},300)
			});
		}
	}
}) 
// TODO: --|-------- run-app-sms
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runAppSms", function(){
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", runApp);
			function runApp(event)
			{
				var phoneNumber = $attrs.phone || "08123456789";
				var textMessage = window.encodeURIComponent($attrs.message) || "Hello";
				if (ionic.Platform.isIOS()){
					var urlSchema = "sms:" + phoneNumber + ";?&body=" + textMessage;
				}else{
					var urlSchema = "sms:" + phoneNumber + "?body=" + textMessage;
				}
				window.open(urlSchema,"_system","location=yes");
			};
		}
	};
})
// TODO: --|-------- run-app-call
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runAppCall", function(){
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", runApp);
			function runApp(event)
			{
				var phoneNumber = $attrs.phone || "08123456789";
				var urlSchema = "tel:" + phoneNumber ;
				window.open(urlSchema,"_system","location=yes");
			};
		}
	};
})
// TODO: --|-------- run-app-email
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runAppEmail", function(){
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", runApp);
			function runApp(event)
			{
				var EmailAddr = $attrs.email || "steffanvernillo@gmail.com";
				var textSubject = $attrs.subject || "";
				var textMessage = window.encodeURIComponent($attrs.message) || "";
				var urlSchema = "mailto:" + EmailAddr + "?subject=" + textSubject + "&body=" + textMessage;
				window.open(urlSchema,"_system","location=yes");
			};
		}
	};
})
// TODO: --|-------- run-app-whatsapp
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runAppWhatsapp", function(){
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", runApp);
			function runApp(event)
			{
				var textMessage = window.encodeURIComponent($attrs.message) || "";
				var urlSchema = "whatsapp://send?text=" + textMessage;
				window.open(urlSchema,"_system","location=yes");
			};
		}
	};
})
// TODO: --|-------- run-app-line
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runAppLine", function(){
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", runApp);
			function runApp(event)
			{
				var textMessage = window.encodeURIComponent($attrs.message) || "";
				var urlSchema = "line://msg/text/" + textMessage;
				window.open(urlSchema,"_system","location=yes");
			};
		}
	};
})
// TODO: --|-------- run-app-twitter
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runAppTwitter", function(){
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", runApp);
			function runApp(event)
			{
				var textMessage = window.encodeURIComponent($attrs.message) || "";
				var urlSchema = "twitter://post?message=" + textMessage;
				window.open(urlSchema,"_system","location=yes");
			};
		}
	};
})
// TODO: --|-------- run-open-url
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runOpenURL", function(){
	/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", runApp);
			function runApp(event)
			{
				var $href = $attrs.href || "http://steffan.esy.es";
				window.open($href,"_system","location=yes");
			};
		}
	};
})
// TODO: --|-------- run-app-browser
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runAppBrowser", function(){
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", runApp);
			function runApp(event)
			{
				var $href = $attrs.href || "http://steffan.esy.es";
				window.open($href,"_blank","closebuttoncaption=Done");
			};
		}
	};
})
// TODO: --|-------- run-webview
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runWebview", function(){
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", runApp);
			function runApp(event)
			{
				var $href = $attrs.href || "http://steffan.esy.es";
				window.open($href,"_self");
			};
		}
	};
})
// TODO: --|-------- run-social-sharing
/** required: cordova-plugin-whitelist, cordova-plugin-inappbrowser **/
.directive("runSocialSharing", function($ionicActionSheet, $timeout){
	return {
			controller: function($scope, $element, $attrs){
			$element.bind("click", showSocialSharing);
			function showSocialSharing(event)
			{
				var hideSheet = $ionicActionSheet.show(
				{
					titleText: 'Share This',
					buttons: [
						{ text: '<i class="ion-social-facebook"></i> Facebook'},
						{ text: '<i class="ion-social-twitter"></i> Twitter'},
						{ text: '<i class="ion-social-whatsapp"></i> Whatsapp'},
						{ text: '<i class="icon-left ion-ios-chatbubble"></i> Line'},
						],
					cancelText: 'Cancel',
					cancel: function(){
						// add cancel code.
					},
					buttonClicked: function(index)
					{
						switch (index)
						{
							case 0:
								var textMessage = window.encodeURIComponent($attrs.message) || "";
								var urlSchema = "https://facebook.com/sharer/sharer.php?u=" + textMessage;
								window.open(urlSchema, "_system", "location=yes");
								break;
							case 1:
								var textMessage = window.encodeURIComponent($attrs.message) || "";
								var urlSchema = "twitter://post?message=" + textMessage;
								window.open(urlSchema, "_system", "location=yes");
								break;
							case 2:
								var textMessage = window.encodeURIComponent($attrs.message) || "";
								var urlSchema = "whatsapp://send?text=" + textMessage;
								window.open(urlSchema, "_system", "location=yes");
								break;
							case 3:
								var textMessage = window.encodeURIComponent($attrs.message) || "";
								var urlSchema = "line://msg/text/" + textMessage;
								window.open(urlSchema, "_system", "location=yes");
								break;
						}
					}
				});
				$timeout(function()
				{
					hideSheet();
				}, 5000); 
			};
		}
	};
})
				

