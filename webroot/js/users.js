/**
 * @fileoverview Users Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * User factory
 */
NetCommonsApp.factory('User', ['$modal', function($modal) {
  return {
    show: function($scope, url) {
      $modal.open({
        templateUrl: url,
        controller: 'User.modal',
        //backdrop: 'static',
        //size: 'lg',
        animation: false,
        scope: $scope
      }).result.then(
          function(result) {},
          function(reason) {}
      );
    }
  }}]
);


/**
 * user modal controller
 */
NetCommonsApp.controller('User.modal', function($scope, $modalInstance) {
  /**
   * dialog cancel
   *
   * @return {void}
   */
  $scope.cancel = function() {
    $modalInstance.dismiss();
  };
});
