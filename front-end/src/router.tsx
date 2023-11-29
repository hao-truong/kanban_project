import { createBrowserRouter } from "react-router-dom";
import RegisterPage from "./application/auth/register/page";
import LoginPage from "./application/auth/login/page";

const router = createBrowserRouter([
  {
    path: "/",
    element: (
      <div>
        <h1>Hello World</h1>
      </div>
    ),
  },
  {
    path: "auth",
    children: [{
      path: "sign-in",
      element: <LoginPage />
    }, {
      path: "register",
      element: <RegisterPage />
    }]
  }
]);

export default router;