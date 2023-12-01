import { createBrowserRouter } from "react-router-dom";
import RegisterPage from "./application/auth/register/page";
import LoginPage from "./application/auth/login/page";
import AuthGuard from "./shared/components/guards/AuthGuard";
import MainLayout from "./shared/components/layouts/MainLayout";
import HomePage from "./application/home/page";
import BoardPage from "./application/board/page";

const router = createBrowserRouter([
  {
    path: "/",
    element: (
      <AuthGuard>
        <MainLayout>
          <HomePage />
        </MainLayout>
      </AuthGuard>
    ),
  },
  {
    path: "boards/:boardId",
    element: (
      <MainLayout>
        <BoardPage />
      </MainLayout>
    ),
  },
  {
    path: "auth",
    children: [{
      path: "sign-in",
      element: (
        <AuthGuard>
          <LoginPage />
        </AuthGuard>
      )
    }, {
      path: "register",
      element: (
        <AuthGuard>
          <RegisterPage />
        </AuthGuard>
      )
    }]
  }
]);

export default router;