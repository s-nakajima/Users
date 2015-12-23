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
      $scope.showUserSearch = function(
              condtions, plugin, controller, action, pass) {
        if (pass) {
          pass = '/' + pass;
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
                  action: action,
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
       * 検索後に戻すプラグイン
       */
      $scope.plugin = options['plugin'];

      /**
       * 検索後に戻すコントローラ
       */
      $scope.controller = options['controller'];

      /**
       * 検索後に戻すアクション
       */
      $scope.action = options['action'];

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
        $scope.condtions = {search: '1'};
        angular.forEach(element.serializeArray(), function(input) {
          if (input['value'] !== '') {
            this.condtions[input['name']] = input['value'];
          }
        }, $scope);

        $location.search($scope.condtions);
        $window.location.href = $scope.baseUrl + '/' + $scope.plugin + '/' +
                $scope.controller + '/' + $scope.action +
                $scope.pass + $location.url();
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
