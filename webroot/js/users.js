/**
 * @fileoverview Users Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * Users controller
 */
NetCommonsApp.controller('Users.controller',
    ['$scope', 'NetCommonsModal', function($scope, NetCommonsModal) {

      /**
       * Show user information method
       *
       * @param {number} users.id
       * @return {void}
       */
      $scope.showUser = function($event, id) {
        NetCommonsModal.show(
            $scope, 'User.view',
            $scope.baseUrl + '/users/users/view/' + id + ''
        );
        $event.preventDefault();
        $event.stopPropagation();
      };
    }]);


/**
 * User modal controller
 */
NetCommonsApp.controller('User.view',
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
