import { useState } from "react";
import JwtStorage from "../storages/JwtStorage";

const useCheckLogin = () => {
    const [isLoggedIn] = useState(!!JwtStorage.getToken() ?? false);

    return isLoggedIn;
};

export default useCheckLogin;