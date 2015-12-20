/**
 * @fileoverview UserSearch Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * UserSearch controller
 */
NetCommonsApp.controller('UserSearch.controller', function(
    $scope, NetCommonsModal) {

      /**
       * 検索ダイアログ表示
       *
       * @param {array} condtions 条件配列
       * @param {string} callbackUrl callbackするURL
       * @return {void}
       */
      $scope.showUserSearch = function(condtions, plugin, controller, pass) {
        if (pass) {
          pass = '/' + pass
        }

        NetCommonsModal.show(
            $scope, 'UserSearch.search',
            $scope.baseUrl + '/' +
                    plugin + '/' + controller + '/search_conditions' + pass,
            {
              backdrop: 'static',
              size: 'lg',
              resolve: {
                options: {
                  condtions: condtions,
                  plugin: plugin,
                  controller: controller,
                  pass: pass
                }
              }
            }
        );
      };
    });


/**
 * UserManager search condtion modal controller
 */
NetCommonsApp.controller('UserSearch.search', function(
    $scope, $http, $modalInstance, $location, $window, options) {

      /**
       * プラグイン
       */
      $scope.plugin = options['plugin'];

      /**
       * コントローラ
       */
      $scope.controller = options['controller'];

      /**
       * URL pass
       */
      $scope.pass = options['pass'];

      /**
       * 検索条件を保持する変数
       */
      $scope.condtions = options['condtions'];

      /**
       * 初期処理
       *
       * @return {void}
       */
      $scope.initialize = function(domId) {
        $scope.domId = domId;
      };

      /**
       * 検索処理
       *
       * @return {void}
       */
      $scope.search = function() {
        var element = angular.element('#' + $scope.domId);
        $scope.condtions = {};
        angular.forEach(element.serializeArray(), function(input) {
          if (input['value'] !== '') {
            this.condtions[input['name']] = input['value'];
          }
        }, $scope);

        //console.log($location.url($scope.condtions));

        $http.post($scope.baseUrl + '/' + $scope.plugin + '/' +
                    $scope.controller + '/search_result' + $scope.pass,
            $.param({_method: 'POST', data: $scope.condtions}),
            {cache: false,
              headers:
                  {'Content-Type': 'application/x-www-form-urlencoded'}
            }
        )
          .success(function(data) {
              //success condition
              $window.location.href = $scope.baseUrl +
                      $scope.plugin + '/' + $scope.controller +
                      '/index' + '?search';
              //$modalInstance.close('success');
            })
          .error(function(data, status) {
              //error condition
              $modalInstance.dismiss('error');
            });
      };

      /**
       * キャンセル処理
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
      };
    });
