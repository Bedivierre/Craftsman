Для добавления нового варианта транспортировки данных необходимо:
1. Создать функцию с сигнатурой 
`func(BaseDataObject $data, BaseRequestObject $request) : BaseResponseObject`,
2. Описать в этой функции механизм передачи и получения данных. 
Функция должна возвращать объект типа `BaseResponseObject`.
3. Назначить эту функцию в конструктор объекта `BaseRequestObject` или указать
её в качестве аргумента функции объекта `BaseRequestObject 
$object->SetMethod(string $name, Callable $func)` 