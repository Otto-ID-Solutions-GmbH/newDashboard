import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AvgLifetimeDeltaComponent } from './avg-lifetime-delta.component';

describe('AvgLifetimeDeltaComponent', () => {
  let component: AvgLifetimeDeltaComponent;
  let fixture: ComponentFixture<AvgLifetimeDeltaComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AvgLifetimeDeltaComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AvgLifetimeDeltaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
