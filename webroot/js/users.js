/**
 * @fileoverview Users Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * User factory
 */
NetCommonsApp.factory('NetCommonsUser',
    ['$http', '$modal', function($http, $modal) {

       return {
         show: function($scope, url) {
            $modal.open({
              templateUrl: url,
              //controller: controller,
              backdrop: 'static',
              scope: $scope
            }).result.then(
                function(result) {},
                function(reason) {}
            );



//              var templateUrl = $scope.PLUGIN_EDIT_URL + 'view/' + $scope.frameId;
//              var controller = 'Announcements.edit';
//
//              $modal.open({
//                templateUrl: templateUrl,
//                controller: controller,
//                backdrop: 'static',
//                scope: $scope
//              }).result.then(
//                  function(result) {},
//                  function(reason) {
//                    $scope.flash.close();
//                  }
//              );
//            };


//           functions.get(editUrl)
//                .success(function(data) {
//                  //最新データセット
//                  if (angular.isFunction(callback)) {
//                    callback(data.results);
//                  }
//                  //ダイアログ呼び出し
//                  functions.showDialog(modalOptions).result.then(
//                      function(result) {},
//                      function(reason) {
//                        if (typeof reason.data === 'object') {
//                          //openによるエラー
//                          NetCommonsFlash.danger(reason.data.name);
//                        } else if (reason === 'canceled') {
//                          //キャンセル
//                          NetCommonsFlash.close();
//                        }
//                      }
//                  );
//                })
//                .error(function(data) {
//                  // TODO: translation
//                  var message = data.name ||
//                                'Network error. Please try again later.';
//                  NetCommonsFlash.danger(message);
//                });

//            $http.get(url, {cache: false})
//              .success(function(data) {
//                  //success condition
//                  deferred.resolve(data);
//                })
//              .error(function(data, status) {
//                  //error condition
//                  deferred.reject(data, status);
//                });


         }
       };
     }]
);
