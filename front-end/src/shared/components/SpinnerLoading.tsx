import { CircularProgress } from '@mui/material';

const SpinnerLoading = () => {
  return (
    <div className="w-full flex items-center justify-center py-10">
      <CircularProgress />
    </div>
  );
};

export default SpinnerLoading;
