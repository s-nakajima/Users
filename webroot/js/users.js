/**
 * @fileoverview Users Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * ncHtmlContent filter
 *
 * @param {string} filter name
 * @param {Array} use service
 */
//NetCommonsApp.filter('handleByUserSelected', ['$sce', function($sce) {
//  return function(user) {
//    var html = '';
//    html += '<img ng-src="' + user.avatar + '" class="user-avatar-xs"> ';
//    html += user.handlename;
//    return html;
//  };
//}]);


/**
 * SelectUser factory
 */
NetCommonsApp.factory('SelectUser',
    ['NetCommonsModal', function(NetCommonsModal) {
      return function($scope, id) {
        return NetCommonsModal.show(
            $scope, 'User.select',
            $scope.baseUrl + '/users/users/select/' + id,
            {
              backdrop: 'static',
              size: 'sm'
            }
        );
      }}]
);


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


/**
 * User search condtion modal controller
 */
NetCommonsApp.controller('User.select', function(
    $scope, $http, $modalInstance) {

      /**
       * Flag searched
       */
      $scope.searched = false;

      /**
       * Candidate users
       */
      $scope.candidates = [];

      /**
       * Favorite users
       */
      $scope.favorites = [];

      /**
       * Initialize action
       *
       * @return {void}
       */
      $scope.initialize = function(domId, favorites) {
        $scope.domId = domId;
        $scope.searched = false;
        $scope.favorites = favorites;
      };

      /**
       * Search action
       *
       * @return {void}
       */
      $scope.search = function($event) {
        if ($event && $event.keyCode !== 13) {
          return;
        }

        var keyword = angular.element('#' + $scope.domId);
        if (! keyword || ! keyword[0].value) {
          $scope.searched = false;
          return;
        }

        var searchUrl = $scope.baseUrl + '/users/users/search.json';
        var options = {
          params: {handlename: keyword[0].value},
          cache: false
        };
        $http.get(searchUrl, options)
          .success(function(data) {
              $scope.candidates = data['users'];
              $scope.searched = true;
            })
            .error(function(data, status) {
              $scope.candidates = [];
            });
      };

      /**
       * Selected user. Dailog close.
       *
       * @return {void}
       */
      $scope.selectedFromCandidates = function(index) {
        $modalInstance.close($scope.candidates[index]);
      };

      /**
       * Selected user. Dailog close.
       *
       * @return {void}
       */
      $scope.selectedFromFavorites = function(index) {
        $modalInstance.close($scope.favorites[index]);
      };

      /**
       * Cancel action. Daialog close
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
      };

    });
