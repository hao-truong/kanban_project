import useCheckLogin from "@/shared/hooks/useCheckLogin";
import React, { useEffect } from "react";
import { useLocation, useNavigate } from "react-router-dom";

const AuthGuard = ({ children }: { children: React.ReactNode }) => {
    const isLogin = useCheckLogin();
    const navigate = useNavigate();
    const location = useLocation();

    useEffect(() => {
        if(location.pathname === "/" && !isLogin) {
            navigate("/auth/sign-in")
        }

        if (location.pathname !== "/" && isLogin) {
            navigate("/");
        } 
    }, [isLogin, navigate, location]);

    return (
        <div>
            <main>{children}</main>
        </div>
    );
};

export default AuthGuard;