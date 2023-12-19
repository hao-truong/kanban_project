import {
  MAX_LENGTH_INPUT_STRING,
  MAX_TITLE_LENGTH,
  MIN_LENGTH_INPUT_STRING,
} from '@/shared/utils/constant';
import * as yup from 'yup';
import { useEffect, useRef } from 'react';
import { SubmitHandler, useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import { toast } from 'react-toastify';
import { TextField } from '@mui/material';
import BoardService from '@/shared/services/BoardService';
import { useQueryClient } from 'react-query';

interface itemProps {
  isOpen: boolean;
  setIsOpen: Function;
  boardId: number;
}

const schemaValidation = yup
  .object({
    member: yup
      .string()
      .trim()
      .required('Member is required')
      .min(
        MIN_LENGTH_INPUT_STRING,
        `Member must be at least ${MIN_LENGTH_INPUT_STRING} characters long`,
      )
      .max(
        MAX_LENGTH_INPUT_STRING,
        `Member must be at least ${MAX_LENGTH_INPUT_STRING} characters long`,
      ),
  })
  .required();

const DialogAddMember = ({ isOpen, setIsOpen, boardId }: itemProps) => {
  const queryClient = useQueryClient();
  const dialogRef = useRef<HTMLDialogElement | null>(null);
  const bodyDialogRef = useRef<HTMLDivElement | null>(null);
  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm<MemberToAddReq>({
    resolver: yupResolver(schemaValidation),
  });
  const onSubmit: SubmitHandler<MemberToAddReq> = (reqData) => {
    BoardService.addMemberToBoard(boardId, reqData)
      .then((response) => {
        toast.success(response.data);
        queryClient.invalidateQueries('getMyBoards');
        reset();
        if (dialogRef.current) {
          dialogRef.current.close();
        }
      })
      .catch((responseError: ResponseError) => {
        toast.error(responseError.message);
      });
  };

  useEffect(() => {
    if (isOpen && dialogRef) {
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
      <div ref={bodyDialogRef} className="flex flex-col items-end justify-center gap-4">
        <form
          className="min-w-[500px] text-center flex flex-col gap-5 rounded-xl"
          onSubmit={handleSubmit(onSubmit)}
        >
          <TextField
            id="outlined-basic"
            label="Member's username"
            variant="outlined"
            {...register('member')}
            error={errors.member ? true : false}
            helperText={errors.member?.message}
            inputProps={{ maxLength: MAX_TITLE_LENGTH }}
          />
          <div className="flex flex-row justify-end">
            <button className="w-fit py-2 px-6 bg-blue-500 text-white rounded-lg" type="submit">
              Add
            </button>
          </div>
        </form>
      </div>
    </dialog>
  );
};

export default DialogAddMember;
