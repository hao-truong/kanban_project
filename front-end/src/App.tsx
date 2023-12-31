import { RouterProvider } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';
import router from './router';
import 'react-toastify/dist/ReactToastify.css';
import './index.css';
import 'react-quill/dist/quill.snow.css';
import { QueryClient, QueryClientProvider } from 'react-query';

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false,
    },
  },
});

function App() {
  return (
    <>
      <QueryClientProvider client={queryClient}>
        <RouterProvider router={router} />
        <ToastContainer position="bottom-right" autoClose={500} />
      </QueryClientProvider>
    </>
  );
}

export default App;
