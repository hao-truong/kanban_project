import { RouterProvider } from "react-router-dom";
import { ToastContainer } from "react-toastify";
import router from "./router";
import "react-toastify/dist/ReactToastify.css";
import "./index.css";

function App() {
  return (
    <>
      <RouterProvider router={router} />
      <ToastContainer position="bottom-right" autoClose={500} />
    </>
  );
}

export default App
