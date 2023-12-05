import useCheckLogin from '@/shared/hooks/useCheckLogin';
import AuthService from '@/shared/services/AuthService';
import UserService from '@/shared/services/UserService';
import { useGlobalState } from '@/shared/storages/GlobalStorage';
import JwtStorage from '@/shared/storages/JwtStorage';
import { useEffect, useRef, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';

const SignInUser = () => {
  const navigate = useNavigate();
  const { user, setUser } = useGlobalState();
  const isLogin = useCheckLogin();
  const [isShowUserMenu, setIsShowUserMenu] = useState<boolean>(false);
  const userRef = useRef<HTMLDivElement | null>(null);
  const menuRef = useRef<HTMLUListElement | null>(null);

  const getMe = async () => {
    try {
      const { data } = await UserService.getProfile();

      setUser(data);
    } catch (error: any) {
      toast.error(error.message);
    }
  };

  useEffect(() => {
    if (isLogin) {
      getMe();
    }
  }, [isLogin]);

  useEffect(() => {
    const handleOutsideClick = (event: any) => {
      if (
        userRef.current &&
        !userRef.current.contains(event.target) &&
        !menuRef.current?.contains(event.target)
      ) {
        setIsShowUserMenu(false);
      }
    };

    document.addEventListener('mousedown', handleOutsideClick);

    return () => {
      document.removeEventListener('mousedown', handleOutsideClick);
    };
  }, [userRef]);

  const handleLogout = async () => {
    const data = await AuthService.logout()
      .then((response) => response.data)
      .catch((responseError: ResponseError) => toast.error(responseError.message));

    if (data) {
      JwtStorage.deleteToken();
      navigate('/auth/sign-in');
    }
  };

  return (
    <div className="relative">
      <div
        ref={userRef}
        className="h-fit w-fit p-2 bg-yellow-400 uppercase cursor-pointer"
        onClick={() => setIsShowUserMenu(!isShowUserMenu)}
      >
        {user?.alias}
      </div>
      {isShowUserMenu && (
        <ul
          ref={menuRef}
          className="absolute flex flex-col items-center bg-slate-200 top-full right-0"
        >
          <li
            className="py-2 w-[200px] hover:bg-slate-400 cursor-pointer text-center"
            onClick={handleLogout}
          >
            Log out
          </li>
        </ul>
      )}
    </div>
  );
};

const MainLayout = ({ children }: { children: React.ReactNode }) => {
  return (
    <div className="container mx-auto">
      <header className="flex flex-row justify-between items-center py-5">
        <Link className="text-4xl" to={'/'}>
          KANBAN BOARD PROBATION
        </Link>
        <SignInUser />
      </header>
      <div className="min-h-[700px]">{children}</div>
      <footer className="text-center">Develop by Truong Van Hao</footer>
    </div>
  );
};

export default MainLayout;
