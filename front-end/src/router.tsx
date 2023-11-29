import { createBrowserRouter } from "react-router-dom";
import RegisterPage from "./application/auth/register/page";
import LoginPage from "./application/auth/login/page";
import AuthGuard from "./shared/components/guards/AuthGuard";
import MainLayout from "./shared/components/layouts/MainLayout";
import KanbanBoard from "./application/home/KanbanBoard";

const router = createBrowserRouter([
  {
    path: "/",
    element: (
      <AuthGuard>
        <MainLayout>
          <KanbanBoard />
        <div>
          <h1>Hello World</h1>
        </div>
        </MainLayout>
      </AuthGuard>
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