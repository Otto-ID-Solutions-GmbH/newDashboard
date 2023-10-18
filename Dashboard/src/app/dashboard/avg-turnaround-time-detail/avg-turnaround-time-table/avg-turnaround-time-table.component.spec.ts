import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AvgTurnaroundTimeTableComponent } from './avg-turnaround-time-table.component';

describe('AvgTurnaroundTimeTableComponent', () => {
  let component: AvgTurnaroundTimeTableComponent;
  let fixture: ComponentFixture<AvgTurnaroundTimeTableComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AvgTurnaroundTimeTableComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AvgTurnaroundTimeTableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
