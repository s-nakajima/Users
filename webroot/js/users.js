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
      return function($scope, userId, roomId) {
        return NetCommonsModal.show(
            $scope, 'User.select',
            $scope.baseUrl + '/users/users/select/' + userId +
                    '?room_id=' + roomId,
            {
              backdrop: 'static',
              resolve: {
                options: {
                  userId: userId,
                  roomId: roomId
                }
              }
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
    $scope, $http, $modalInstance, filterFilter, options) {

      /**
       * User id
       */
      $scope.userId = options['userId'];

      /**
       * Room id
       */
      $scope.roomId = options['roomId'];

      /**
       * Keyword
       */
      $scope.keyword = null;

      /**
       * Flag searched
       */
      $scope.searched = false;

      /**
       * Candidate users
       */
      $scope.candidates = [];

      /**
       * Paginator
       */
      $scope.paginator = {};

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
      };

      /**
       * Search action
       *
       * @return {void}
       */
      var searchUsers = function(keyword, page) {
        if (! keyword) {
          return;
        }
        var searchUrl = $scope.baseUrl + '/users/users/search';
        if (page) {
          searchUrl += '/page:' + page;
        }
        var options = {
          params: {
            //room_id: $scope.roomId,
            handlename: keyword
          },
          cache: false
        };

        $http.get(searchUrl, options)
          .success(function(data) {
              $scope.candidates = data['users'];
              $scope.searched = true;
              $scope.keyword = keyword;
              $scope.paginator = data['paginator'];

              var startPage = $scope.paginator.startPage;
              var endPage = $scope.paginator.endPage;
              $scope.pages = [];
              for (var i = startPage; i <= endPage; i++) {
                $scope.pages.push(i);
              }
            })
            .error(function(data, status) {
              $scope.candidates = [];
              $scope.keyword = null;
              $scope.paginator = {};
              $scope.pages = [];
            });
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
          return;
        }

        searchUsers(keyword[0].value);
      };

      /**
       * Move pages
       *
       * @return {void}
       */
      $scope.movePage = function(page) {
        searchUsers($scope.keyword, page);
      };

      /**
       * Select user.
       *
       * @return {void}
       */
      $scope.select = function(index) {
        var result = filterFilter($scope.candidates, $scope.candidates[index]);
        if (! $scope.selected(result[0])) {
          $scope.selectors.push(result[0]);
        }
      };

      /**
       * Selected user.
       *
       * @return {bool}
       */
      $scope.selected = function(obj) {
        var result = filterFilter($scope.selectors, obj);
        return !(result.length === 0);
      };

      /**
       * Select removed user
       *
       * @return {void}
       */
      $scope.remove = function(index) {
        $scope.selectors.splice(index, 1);
      };

      /**
       * Clear select user
       *
       * @return {void}
       */
      $scope.clear = function() {
        $scope.selectors = [];
      };

      /**
       * Save user. Dailog close.
       *
       * @return {void}
       */
      $scope.save = function() {


        $modalInstance.close($scope.selectors);
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
