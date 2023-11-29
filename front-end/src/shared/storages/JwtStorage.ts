const isClient = typeof window !== "undefined";
import Cookies from "js-cookie";

const JwtStorage = () => {
  let inMemoryToken: AuthToken | null = null;

  const getToken = () => {
    if (!isClient) {
      return;
    }

    if (
      Cookies.get("accessToken") &&
      inMemoryToken === null &&
      Cookies.get("refreshToken")
    ) {
      inMemoryToken = {
        accessToken: Cookies.get("accessToken") ?? "",
        refreshToken: Cookies.get("refreshToken") ?? "",
      };
    }

    return inMemoryToken;
  };

  const setToken = (token: AuthToken) => {
    isClient && Cookies.set("accessToken", token.accessToken, { expires: 1 });
    isClient && Cookies.set("refreshToken", token.refreshToken, { expires: 7 });
    inMemoryToken = token;
  };

  const deleteToken = () => {
    inMemoryToken = null;
    isClient && Cookies.remove("accessToken");
    isClient && Cookies.remove("refreshToken");
  };

  return {
    getToken,
    setToken,
    deleteToken,
  };
};

export default JwtStorage();