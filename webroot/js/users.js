/**
 * @fileoverview Users Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * SelectUser factory
 *
 * #### 結果サンプル
 *
 * $modal.open(options).result.then(function(result) {}, function() {})
 * resultの中身のサンプル
 *   {
 *     id: "2",
 *     handlename: "編集長ユーザ",
 *     link: "/users/users/view/2",
 *     avatar: "/users/users/download/2/avatar/thumb",
 *   }
 *
 * @link https://angular-ui.github.io/bootstrap/#/modal
 */
NetCommonsApp.factory('SelectUser',
    ['NetCommonsModal', function(NetCommonsModal) {
      return function($scope, id) {
        return NetCommonsModal.show(
            $scope, 'User.select',
            $scope.baseUrl + '/users/users/select/' + id,
            {
              backdrop: 'static'
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
    $scope, $http, $modalInstance, filterFilter) {

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
      $scope.selectors = [];

      /**
       * Initialize action
       *
       * @return {void}
       */
      $scope.initialize = function(domId) {
        $scope.domId = domId;
        $scope.searched = false;
//        $scope.selectors = selectors;
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
       * Selected user.
       *
       * @return {void}
       */
      $scope.select = function(index) {
        var result = filterFilter($scope.candidates, $scope.candidates[index]);
        console.log(result);
        $scope.selectors.push(result[0]);

        //$modalInstance.close($scope.candidates[index]);
      };

      /**
       * Select removed user
       *
       * @return {void}
       */
      $scope.remove = function(index) {
        $scope.selectors.splice(index, 1);

        //$modalInstance.close($scope.candidates[index]);
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
