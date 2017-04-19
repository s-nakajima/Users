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
            ).result.then(function(type) {
              console.log(type);
            });
          }
//          if (angular.isObject($event)) {
//            $event.preventDefault();
//            $event.stopPropagation();
//          }
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
            ).result.then(function(type) {
              console.log(type);
            });
          }
//          if (angular.isObject($event)) {
//            $event.preventDefault();
//            $event.stopPropagation();
//          }
        };
      }]);


/**
 * User modal controller
 */
NetCommonsApp.controller('UserDelete.confirm',
    ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {

      /**
       * dialog cancel
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
      };
    }]);
