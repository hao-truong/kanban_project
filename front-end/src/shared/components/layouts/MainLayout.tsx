import AuthService from "@/shared/services/AuthService";
import UserService from "@/shared/services/UserService";
import { useGlobalState } from "@/shared/storages/GlobalStorage";
import JwtStorage from "@/shared/storages/JwtStorage";
import { useEffect, useRef, useState } from "react";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";

const SignInUser = () => {
    const navigate = useNavigate();
    const { user, setUser } = useGlobalState();
    const [isShowUserMenu, setIsShowUserMenu] = useState<boolean>(false)
    const userRef = useRef<HTMLDivElement | null>(null);
    const menuRef = useRef<HTMLUListElement | null>(null);

    useEffect(() => {
        const getMe = async () => {
            try {
                const { data } = await UserService.getProfile();

                setUser(data);
            } catch (error: any) {
                toast.error(error.message);
            }
        }

        getMe();
    }, [])

    useEffect(() => {
        const handleOutsideClick = (event: any) => {
            if (userRef.current && !userRef.current.contains(event.target) && !menuRef.current?.contains(event.target)) {
                setIsShowUserMenu(false);
            }
        };

        document.addEventListener("mousedown", handleOutsideClick);

        return () => {
            document.removeEventListener("mousedown", handleOutsideClick);
        };
    }, [userRef]);

    const handleLogout = async () => {
        try {
            const {data} = await AuthService.logout();

            JwtStorage.deleteToken();
            toast.success(data);
            navigate("/auth/sign-in");
        } catch (error: any) {
            toast.error(error.message);
        }
    }

    return (
        <div className="relative">
            <div ref={userRef} className="h-fit w-fit p-2 bg-yellow-400 uppercase cursor-pointer" onClick={() => setIsShowUserMenu(!isShowUserMenu)}>
                {user?.alias}
            </div>
            {
                isShowUserMenu &&
                <ul ref={menuRef} className="absolute flex flex-col items-center bg-slate-200 top-full right-0">
                    <li className="py-2 w-[200px] hover:bg-slate-400 cursor-pointer text-center" onClick={handleLogout}>
                        Log out
                    </li>
                </ul>
            }
        </div>
    )
}

const MainLayout = ({ children }: { children: React.ReactNode }) => {


    return (
        <div className="container mx-auto">
            <header className="flex flex-row justify-between items-center py-5">
                <h1 className="text-4xl">
                    KANBAN BOARD PROBATION
                </h1>
                <SignInUser />
            </header>
            <div>
                {children}
            </div>
            <footer className="text-center">
                Develop by Truong Van Hao
            </footer>
        </div>
    );
};

export default MainLayout;