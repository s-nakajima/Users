/**
 * @fileoverview Users Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * Users controller
 */
NetCommonsApp.controller('UserDelete.controller',
    ['$scope', 'NetCommonsModal', 'NC3_URL', 'LOGIN_USER',
      function($scope, NetCommonsModal, NC3_URL, LOGIN_USER) {

        /**
         * 退会規約
         *
         * @param {number} users.id
         * @return {void}
         */
        $scope.showDisclaimer = function($event, id) {
          if (id == LOGIN_USER.id) {
            NetCommonsModal.show(
                $scope, 'UserDelete.confirm',
                NC3_URL + '/users/users/delete_disclaimer/' + LOGIN_USER.id
            ).result.then(
                function() {
                  //Success
                  $scope.showConfirm($event, id);
                },
                function() {
                  //Error
                }
            );
          }
        };

        /**
         * Show user information method
         *
         * @param {number} users.id
         * @return {void}
         */
        $scope.showConfirm = function($event, id) {
          if (id == LOGIN_USER.id) {
            NetCommonsModal.show(
                $scope, 'UserDelete.confirm',
                NC3_URL + '/users/users/delete_confirm/' + LOGIN_USER.id
            );
          }
        };
      }]);


/**
 * User modal controller
 */
NetCommonsApp.controller('UserDelete.confirm',
    ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {

      /**
       * Post data
       */
      $scope.data = null;

      /**
       * 初期処理
       *
       * @return {void}
       */
      $scope.initialize = function(data) {
        $scope.data = data;
      };

      /**
       * dialog cancel
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
      };

      /**
       * dialog disclaimer
       *
       * @return {void}
       */
      $scope.disclaimer = function() {
        $uibModalInstance.close('disclaimer');
      };
    }]);
