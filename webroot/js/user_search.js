/**
 * @fileoverview UserSearch Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * UserSearch controller
 */
NetCommonsApp.controller('UserSearch.controller',
    ['$scope', 'NetCommonsModal', '$location', 'NC3_URL',
      function($scope, NetCommonsModal, $location, NC3_URL) {

        /**
         * 検索ダイアログ表示
         *
         * @param {array} conditions 条件配列
         * @param {string} callbackUrl callbackするURL
         * @return {void}
         */
        $scope.showUserSearch = function(
                conditions, plugin, controller, action, pass) {
          if (pass) {
            pass = '/' + pass;
          }

          $location.search(conditions);

          NetCommonsModal.show(
              $scope, 'UserSearch.search',
              NC3_URL + '/' +
                  plugin + '/' + controller + '/search_conditions' + pass + $location.url(),
              {
                backdrop: 'static',
                size: 'lg',
                resolve: {
                  options: {
                    conditions: conditions,
                    plugin: plugin,
                    controller: controller,
                    action: action,
                    pass: pass
                  }
                }
              }
          );
        };
      }]);


/**
 * UserManager search condtion modal controller
 */
NetCommonsApp.controller('UserSearch.search',
    ['$scope', '$uibModalInstance', '$location', '$window', 'options', 'NC3_URL',
      function($scope, $uibModalInstance, $location, $window, options, NC3_URL) {

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
        $scope.conditions = options['conditions'];

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
          $scope.conditions = {search: '1'};
          angular.forEach(element.serializeArray(), function(input) {
            if (input['value'] !== '') {
              this.conditions[input['name']] = input['value'];
            }
          }, $scope);

          $location.search($scope.conditions);
          $window.location.href = NC3_URL + '/' + $scope.plugin + '/' +
                  $scope.controller + '/' + $scope.action +
                  $scope.pass + $location.url();
        };

        /**
         * キャンセル処理
         *
         * @return {void}
         */
        $scope.cancel = function() {
          $uibModalInstance.dismiss('cancel');
        };
      }]);
