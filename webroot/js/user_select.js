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
      return function($scope, userId, roomId, selectors) {
        return NetCommonsModal.show(
            $scope, 'User.select',
            //$scope.baseUrl + '/users/users/select/' + userId + '/' +
            //                  '?room_id=' + roomId,
            $scope.baseUrl + '/users/users/select/' + userId + '/' +
                              Math.random() + '?room_id=' + roomId,
            {
              backdrop: 'static',
              resolve: {
                options: {
                  userId: userId,
                  roomId: roomId,
                  selectors: selectors
                }
              }
            }
        );
      }}]
);


/**
 * User search condtion modal controller
 */
NetCommonsApp.controller('User.select', function(
    $scope, $http, $q, $uibModalInstance, filterFilter, options) {

      /**
       * ユーザIDを保持する変数
       */
      $scope.userId = options['userId'];

      /**
       * ルームIDを保持する変数
       */
      $scope.roomId = options['roomId'];

      /**
       * 検索キーワード(ハンドル)を保持する変数
       */
      $scope.keyword = null;

      /**
       * 検索フィールドを保持する変数
       */
      $scope.field = null;

      /**
       * 検索したかどうかのフラグを保持する変数
       */
      $scope.searched = false;

      /**
       * 検索結果を保持する配列
       */
      $scope.searchResults = [];

      /**
       * ページネーションを保持する変数
       */
      $scope.paginator = null;

      /**
       * 選択したユーザを保持する配列
       */
      $scope.selectors = options['selectors'];

      /**
       * Post data
       */
      $scope.data = null;

      /**
       * 初期処理
       *
       * @return {void}
       */
      $scope.initialize = function(domId, searchResults, data, field) {
        $scope.domId = domId;
        $scope.field = field;
        $scope.data = data;
        if (angular.isArray(searchResults) && searchResults.length > 0) {
          $scope.searched = true;
          $scope.searchResults = searchResults;
        }
      };

      /**
       * 検索処理
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
       * ページ移動処理
       *
       * @return {void}
       */
      $scope.movePage = function(page) {
        searchUsers($scope.keyword, page);
      };

      /**
       * 選択処理
       *
       * @return {void}
       */
      $scope.select = function(index) {
        var result = filterFilter($scope.searchResults,
                                  $scope.searchResults[index]);

        if (! angular.isArray($scope.selectors)) {
          $scope.selectors = [];
        }
        if (! $scope.selected(result[0])) {
          $scope.selectors.push(result[0]);
        }
      };

      /**
       * 選択しているかどうかチェックする
       *
       * @return {bool}
       */
      $scope.selected = function(obj) {
        if (! angular.isArray($scope.selectors)) {
          return false;
        }
        var result = filterFilter($scope.selectors, obj);
        return !(result.length === 0);
      };

      /**
       * 選択の解除処理
       *
       * @return {void}
       */
      $scope.remove = function(index) {
        $scope.selectors.splice(index, 1);
      };

      /**
       * 選択クリア処理
       *
       * @return {void}
       */
      $scope.clear = function() {
        $scope.selectors = [];
      };

      /**
       * 決定処理＆ダイアログ閉じる
       *
       * @return {void}
       */
      $scope.save = function() {
        angular.forEach($scope.selectors, function(selector) {
          this.data.UserSelectCount.user_id.push(selector.id);
        }, $scope);

        saveUserSelectCount()
          .success(function(data) {
              $uibModalInstance.close($scope.selectors);
            })
          .error(function(data, status) {
              $uibModalInstance.dismiss('error');
            });
      };

      /**
       * キャンセル処理＆ダイアログ閉じる
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
      };

      /**
       * ユーザ検索処理関数
       *
       * @return {void}
       */
      var searchUsers = function(keyword, page) {
        var searchUrl = $scope.baseUrl + '/users/users/search/' + $scope.userId;
        if (page) {
          searchUrl += '/page:' + page;
        }

        var options = {
          cache: false
        };
        options['params'] = {};
        options['params'][$scope.field] = keyword;
        options['params']['room_id'] = $scope.roomId;

        $http.get(searchUrl, options)
          .success(function(data) {
              $scope.searchResults = data['users'];
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
              $scope.searchResults = [];
              $scope.keyword = null;
              $scope.paginator = {};
              $scope.pages = [];
            });
      };

      /**
       * ユーザ選択した件数更新処理関数
       *
       * @return {Function}
       */
      var saveUserSelectCount = function() {
        var deferred = $q.defer();
        var promise = deferred.promise;

        $http.get('/net_commons/net_commons/csrfToken.json')
          .success(function(token) {
              $scope.data._Token.key = token.data._Token.key;

              //POSTリクエスト
              $http.post(
                  '/users/users/select/' + $scope.userId,
                  $.param({_method: 'POST', data: $scope.data}),
                  {cache: false,
                    headers:
                        {'Content-Type': 'application/x-www-form-urlencoded'}
                  }
              )
              .success(function(data) {
                    //success condition
                    deferred.resolve(data);
                  })
              .error(function(data, status) {
                    //error condition
                    deferred.reject(data, status);
                  });
            })
          .error(function(data, status) {
              //Token error condition
              deferred.reject(data, status);
            });

        promise.success = function(fn) {
          promise.then(fn);
          return promise;
        };

        promise.error = function(fn) {
          promise.then(null, fn);
          return promise;
        };

        return promise;
      };

    });
