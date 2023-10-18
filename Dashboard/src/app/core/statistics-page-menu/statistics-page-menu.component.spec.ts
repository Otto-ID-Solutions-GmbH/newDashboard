import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { StatisticsPageMenuComponent } from './statistics-page-menu.component';

describe('StatisticsPageMenuComponent', () => {
  let component: StatisticsPageMenuComponent;
  let fixture: ComponentFixture<StatisticsPageMenuComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ StatisticsPageMenuComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StatisticsPageMenuComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
