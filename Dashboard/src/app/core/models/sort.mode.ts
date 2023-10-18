export class SortInfo {
  column: string;
  dir: 'asc' | 'desc' = 'asc';


  constructor(column?: string, dir?: "asc" | "desc") {
    this.column = column ? column : null;
    this.dir = dir ? dir : 'asc';
  }
}