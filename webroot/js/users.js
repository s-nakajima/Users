/**
 * @fileoverview Users Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * Users controller
 */
NetCommonsApp.controller('Users.controller', function(
    $scope, NetCommonsModal) {

      /**
       * Show user information method
       *
       * @param {number} users.id
       * @return {void}
       */
      $scope.showUser = function(id) {
        NetCommonsModal.show(
            $scope, 'User.view',
            $scope.baseUrl + '/users/users/view/' + id + ''
        );
      };
    });


/**
 * User modal controller
 */
NetCommonsApp.controller('User.view', function($scope, $modalInstance) {
  /**
   * dialog cancel
   *
   * @return {void}
   */
  $scope.cancel = function() {
    $modalInstance.dismiss('cancel');
  };
});
