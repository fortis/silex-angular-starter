angular.module('MainController', []).controller('MainController', ['$scope', '$location', '$localStorage', 'User',
  function ($scope, $location, $localStorage, User) {
    /**
     * Responsible for highlighting the currently active menu item in the navbar.
     *
     * @param route
     * @returns {boolean}
     */
    $scope.isActive = function (route) {
      return route === $location.path();
    };
  }
]);
