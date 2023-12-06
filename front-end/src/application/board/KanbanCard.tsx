import { MAX_TITLE_LENGTH, MIN_LENGTH_INPUT_STRING } from '@/shared/utils/constant';
import Helper from '@/shared/utils/helper';
import { yupResolver } from '@hookform/resolvers/yup';
import { OutlinedInput } from '@mui/material';
import { Check, MoreHorizontal, Pencil, X } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { SubmitHandler, useForm } from 'react-hook-form';
import * as yup from 'yup';

interface itemProps {
  card: Card;
}

const schemaValidation = yup
  .object({
    title: yup
      .string()
      .trim()
      .required('Title is required!')
      .min(MIN_LENGTH_INPUT_STRING, `Title must be at least ${MAX_TITLE_LENGTH} characters long`)
      .max(MAX_TITLE_LENGTH, `Title must be at least ${MAX_TITLE_LENGTH} characters long`),
  })
  .required();

const KanbanCard = ({ card }: itemProps) => {
  const [isShowMenu, setIsShowMenu] = useState<boolean>(false);
  const [isEditTitle, setIsEditTitle] = useState<boolean>(false);
  const titleRef = useRef<HTMLFormElement | null>(null);
  const {
    register,
    handleSubmit,
    formState: { errors },
    setValue,
  } = useForm<UpdateColumnReq>({
    resolver: yupResolver(schemaValidation),
  });
  const onSubmit: SubmitHandler<UpdateColumnReq> = async (reqData) => {
    console.log(reqData);
  };

  useEffect(() => {
    if (!isEditTitle) {
      setValue('title', card.title);
    }
  }, [isEditTitle, card]);

  return (
    <div
      className="w-full grid grid-cols-12 bg-white rounded-md p-4 gap-2 cursor-pointer"
      draggable
    >
      <div className="col-span-10">
        <div className="">
          {!isEditTitle && (
            <h2 className="break-words text-left hover:decoration-solid hover:underline relative">
              <span>{card.title}</span>
              <Pencil
                className="absolute bottom-0 right-0 hover:bg-slate-200 p-1"
                size={25}
                onClick={() => setIsEditTitle(true)}
              />
            </h2>
          )}
          {isEditTitle && (
            <form ref={titleRef} className="relative" onSubmit={handleSubmit(onSubmit)}>
              <OutlinedInput
                autoFocus
                className="w-full bg-white"
                onFocus={() => Helper.handleOutSideClick(titleRef, setIsEditTitle)}
                id="outlined-adornment-weight"
                aria-describedby="outlined-weight-helper-text"
                {...register('title')}
                error={errors.title ? true : false}
                inputProps={{ maxLength: MAX_TITLE_LENGTH }}
              />
              <div className="flex flex-row justify-end gap-2 mt-2 absolute right-0">
                <button type="submit">
                  <Check
                    size={40}
                    className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer"
                  />
                </button>
                <X
                  size={40}
                  className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer"
                  onClick={() => setIsEditTitle(false)}
                />
              </div>
            </form>
          )}
        </div>
      </div>
      <div className="col-span-2 flex flex-col justify-between items-end gap-4">
        <div className="relative w-fit bg-slate-200 ">
          <MoreHorizontal
            className="cursor-pointer hover:bg-slate-100 p-2"
            size={40}
            onClick={() => setIsShowMenu(!isShowMenu)}
          />
          {isShowMenu && (
            <ul className="absolute bg-white top-full right-0 py-1 w-max shadow-lg">
              <div>
                <li className="py-1 px-4 text-left hover:bg-slate-100 cursor-pointer">Delete</li>
              </div>
              <div>
                <li className="py-1 px-4 text-left hover:bg-slate-100 cursor-pointer">
                  Assign to member
                </li>
              </div>
            </ul>
          )}
        </div>
        <div className="w-full">
          <h2 className="p-2 bg-yellow-400 w-full">#</h2>
        </div>
      </div>
    </div>
  );
};

export default KanbanCard;
