import {
  MAX_LENGTH_INPUT_STRING,
  MAX_TITLE_LENGTH,
  MIN_LENGTH_INPUT_STRING,
} from '@/shared/utils/constant';
import { yupResolver } from '@hookform/resolvers/yup';
import { TextField } from '@mui/material';
import { useEffect, useRef, useState } from 'react';
import { SubmitHandler, useForm } from 'react-hook-form';
import { toast } from 'react-toastify';
import * as yup from 'yup';
import { useQueryClient } from 'react-query';
import ColumnService from '@/shared/services/ColumnService';

interface itemProps {
  isOpen: boolean;
  setIsOpen: Function;
  boardId: number;
}

const schemaValidation = yup
  .object({
    title: yup
      .string()
      .trim()
      .required('Title is required')
      .min(
        MIN_LENGTH_INPUT_STRING,
        `Title must be at least ${MIN_LENGTH_INPUT_STRING} characters long`,
      )
      .max(
        MAX_LENGTH_INPUT_STRING,
        `Title must be at least ${MAX_LENGTH_INPUT_STRING} characters long`,
      ),
  })
  .required();

const DialogCreateColumn = ({ isOpen, setIsOpen, boardId }: itemProps) => {
  const queryClient = useQueryClient();
  const [error, setError] = useState<string | null>(null);
  const dialogRef = useRef<HTMLDialogElement | null>(null);
  const bodyDialogRef = useRef<HTMLDivElement | null>(null);
  const {
    register,
    handleSubmit,
    setValue,
    formState: { errors },
  } = useForm<ColumnReq>({
    resolver: yupResolver(schemaValidation),
  });
  const onSubmit: SubmitHandler<ColumnReq> = (columnReqData) => {
    ColumnService.createColumn(boardId, columnReqData)
      .then(() => {
        setValue('title', '');
        setError(null);
        toast.success('Create new columnn successfully!');
        queryClient.invalidateQueries(`getColumnsOfBoard${boardId}`);
        if (dialogRef.current) {
          dialogRef.current.close();
        }
      })
      .catch((responseError: ResponseError) => setError(responseError.message));
  };

  useEffect(() => {
    if (isOpen && dialogRef) {
      setValue('title', '');
      dialogRef.current?.showModal();
    }

    const handleOutsideClick = (event: any) => {
      if (
        dialogRef.current &&
        bodyDialogRef.current &&
        !bodyDialogRef.current.contains(event.target)
      ) {
        dialogRef.current?.close();
        setIsOpen(false);
      }
    };

    document.addEventListener('mousedown', handleOutsideClick);

    return () => {
      document.removeEventListener('mousedown', handleOutsideClick);
    };
  }, [isOpen, dialogRef, bodyDialogRef]);

  return (
    <dialog ref={dialogRef} className=" rounded-lg p-10">
      <div ref={bodyDialogRef} className="flex flex-col items-center justify-center gap-4">
        {error && <h2 className="text-red-600 text-center">{error}</h2>}
        <form
          className="min-w-[500px] text-center flex flex-col gap-5 rounded-xl"
          onSubmit={handleSubmit(onSubmit)}
        >
          <TextField
            id="outlined-basic"
            label="Column's title"
            variant="outlined"
            {...register('title')}
            error={errors.title ? true : false}
            helperText={errors.title?.message}
            inputProps={{ maxLength: MAX_TITLE_LENGTH }}
          />
          <div className="flex flex-row justify-end">
            <button className="w-fit py-2 px-6 bg-blue-500 text-white rounded-lg" type="submit">
              Create
            </button>
          </div>
        </form>
      </div>
    </dialog>
  );
};

export default DialogCreateColumn;
