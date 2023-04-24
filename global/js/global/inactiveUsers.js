$(document).ready(function () {
  findInactiveUsers = async () => {
    try {
      result = await $.ajax({
        url: '/api/checkLastLoginUsers',
      });
      // console.log(result);
    } catch (error) {
      console.log(error);
    }
  };

  findInactiveUsers();
});
