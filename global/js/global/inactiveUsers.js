$(document).ready(function () {
  findInactiveUsers = async () => {
    try {
      result = await $.ajax({
        url: '/api/checkLastLoginUsers',
      }); 
    } catch (error) {
      console.log(error);
    }
  };

  findInactiveUsers();
});
