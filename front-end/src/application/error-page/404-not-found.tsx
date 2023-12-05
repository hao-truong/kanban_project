import { ArrowLeftSquare } from 'lucide-react';
import { Link } from 'react-router-dom';

const PageNotFound = () => {
  return (
    <div className="h-screen w-screen text center flex flex-col items-center justify-center gap-10">
      <span className="text-4xl">Page not found</span>
      <Link to={'/'} className="flex  items-center justify-center gap-4">
        <ArrowLeftSquare size={40} />
        <span>Back home</span>
      </Link>
    </div>
  );
};

export default PageNotFound;
