/*
    Hereby i am up to use bare Vanilla JS, 
    but with modern syntax and features.
    In test purpose i'am not gonna use transpilers or polyfills
    so this script should be run in modern(!) browsers    

    I'am also not going to build RESTful api, beacause it needs proper server settings,
    so the api will have only one endpoint.
*/

const bannerApp = {
  apiPath: "http://infuse/main.php", //please change it to correct host and path
  imagesPath: "/src",
  defaultImageName: "empty.jpg",
  currentBannerSrc: null,
  domImageContainer: null,
  domCounterContainer: null,

  init: function () {
    this.domImageContainer = document.getElementById("banner");
    this.domCounterContainer = document.getElementById("value");
    this.changeBannerImage();
  },

  changeBannerImage: async function () {
    let src = await this.getNewBannerSrc();
    if (src) {
      let bannerFullUrl = this.imagesPath + "/" + src;
      let bannerImage = new Image();

      bannerImage.onload = () => {
        this.domImageContainer.src = bannerFullUrl;
        this.currentBannerSrc = src;
        this.increaseBannerCounter();
      };
      bannerImage.onerror = () => {
        this.domImageContainer.src =
          this.imagesPath + "/" + this.defaultImageName;
        this.domCounterContainer.innerHTML = "----";
        this.error(`Image ${src} load failed`);
      };
      bannerImage.src = bannerFullUrl;
    }
  },

  getNewBannerSrc: async function () {
    let result = await this.query({
      action: "getNewBannerSrc",
    });
    if (result?.src) {
      return result?.src;
    }
    return false;
  },

  increaseBannerCounter: async function () {
    await this.query({
      action: "increaseBannerCounter",
      src: this.currentBannerSrc,
    });
    this.upadteBannerCouter();
  },

  upadteBannerCouter: async function () {
    if (this.currentBannerSrc) {
      console.log("ab");
      let result = await this.query({
        action: "getBannerStats",
        src: this.currentBannerSrc,
      });
      if (result?.counterValue) {
        this.domCounterContainer.innerHTML = result?.counterValue;
      }
    }
  },

  query: async function (queryBody = {}) {
    let queryResult = false;
    try {
      let response = await fetch(this.apiPath, {
        method: "POST",
        headers: {
          "Content-Type": "application/json;charset=utf-8",
        },
        body: JSON.stringify(queryBody),
      }).catch(this.error);

      if (response.ok) {
        queryResult = await response.json().catch(this.error);

        if (queryResult?.error) {
          //Fetch and read succeded, but the backend(!) has returend an error
          this.error(queryResult?.error);
        }
      } else {
        //Responce is not ok...
        this.error(response.status);
      }
    } catch (e) {
      //Fetch totally crashed
      this.error(e);
    }
    return queryResult;
  },

  error: function (error = "Unknown error") {
    console.log("Handled BANNERAPP error", error);
  },
};

bannerApp.init();
